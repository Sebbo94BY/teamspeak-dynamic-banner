<?php

namespace App\Support;

use App\Http\Controllers\Helpers\TeamSpeakVirtualserver;
use App\Models\Instance;
use App\Support\TeamSpeak\TeamSpeakCapabilities;
use App\Support\TeamSpeak\TeamSpeakCapability;
use App\Support\TeamSpeak\TeamSpeakConnectionFailure;
use PlanetTeamSpeak\TeamSpeak3Framework\Node\Server;
use Throwable;

/**
 * Runs the read-only ServerQuery operations used by the banner bot.
 *
 * Checking the operations, instead of guessing at TeamSpeak permission IDs,
 * also works with custom server groups and TeamSpeak server versions.
 */
class InstanceHealthCheck
{
    public function __construct(private readonly TeamSpeakCapabilities $capabilities)
    {
    }

    /** @return array{healthy: bool, connection_unusable: bool, checks: array<int, array{label: string, healthy: bool, message: string, permissions: array<int, string>}>, channel_list: array} */
    public function check(Instance $instance): array
    {
        $checks = [];
        $channelList = [];

        try {
            // Health checks run alongside the long-lived bot. Use a temporary
            // nickname from the first attempt so the expected duplicate bot
            // nickname never turns a healthy check into an error.
            $virtualserver = (new TeamSpeakVirtualserver($instance))
                ->with_temporary_nickname()
                ->get_virtualserver_connection();
            $checks[] = $this->success('health_check_connection', $this->capabilities->connectionPermissions($instance));
        } catch (Throwable $exception) {
            $checks[] = $this->failure('health_check_connection', $exception, $this->capabilities->connectionPermissions($instance));

            return ['healthy' => false, 'connection_unusable' => true, 'checks' => $checks, 'channel_list' => $channelList];
        }

        return $this->checkConnected($virtualserver, $checks, $channelList);
    }

    /** Runs a health check and persists its result for the non-blocking UI. */
    public function checkAndStore(Instance $instance): array
    {
        $result = $this->check($instance);
        $this->storeResult($instance, $result);

        return $result;
    }

    /** @param array{healthy: bool, checks: array<int, array{label: string, healthy: bool, message: string, permissions: array<int, string>}>} $result */
    public function storeResult(Instance $instance, array $result): void
    {
        $now = now();
        $attributes = [
            'health_last_check_healthy' => $result['healthy'],
            'health_last_check_results' => $result['checks'],
            'health_channel_list' => array_map(fn ($channel) => [
                'cid' => (int) $channel->cid,
                'channel_name' => (string) $channel->channel_name,
            ], $result['channel_list'] ?? []),
            'health_last_checked_at' => $now,
        ];

        $runtimeErrorActive = $this->hasUnresolvedRuntimeError($instance);

        if ($result['healthy']) {
            // A standalone check may use a newer connection than the running
            // bot. It must not resolve a runtime error reported by that bot.
            // Only recordRuntimeSuccess() may do that.
            if (! $runtimeErrorActive) {
                $attributes['health_problem_started_at'] = null;
            }
        } else {
            $failedChecks = array_filter($result['checks'], fn (array $check) => ! $check['healthy']);
            if (! $runtimeErrorActive) {
                $attributes['health_last_error'] = implode("\n", array_map(
                    fn (array $check) => $check['label'].': '.$check['message'],
                    $failedChecks
                ));
                $attributes['health_last_error_at'] = $now;
            }
            $attributes['health_problem_started_at'] = $instance->health_problem_started_at ?? $now;
        }

        $instance->saveOperationalState($attributes);
    }

    /** Stores a bot runtime error immediately, including a useful permission name when possible. */
    public function recordRuntimeFailure(Instance $instance, string $message): void
    {
        $now = now();
        $instance->saveOperationalState([
            'health_last_error' => $this->formatRuntimeFailure($message),
            'health_last_error_at' => $now,
            'health_last_runtime_error_at' => $now,
            'health_problem_started_at' => $instance->health_problem_started_at ?? $now,
        ]);
    }

    /** Marks the running bot as healthy after a complete cache refresh. */
    public function recordRuntimeSuccess(Instance $instance): void
    {
        $now = now();
        $attributes = [
            'health_last_success_at' => $now,
        ];

        if ($instance->health_last_check_healthy !== false) {
            $attributes['health_problem_started_at'] = null;
        }

        $instance->saveOperationalState($attributes);
    }

    /**
     * Checks every ServerQuery capability used by the bot on an existing connection.
     * This is also used once during bot startup, before it starts listening for events.
     *
     * @param array<int, array{label: string, healthy: bool, message: string, permissions: array<int, string>}> $checks
     * @param array<int, mixed> $channelList
     * @return array{healthy: bool, connection_unusable: bool, checks: array<int, array{label: string, healthy: bool, message: string, permissions: array<int, string>}>, channel_list: array}
     */
    public function checkConnected(Server $virtualserver, array $checks = [], array $channelList = []): array
    {
        $context = ['channel_list' => $channelList];
        $connectionUnusable = false;
        foreach ($this->capabilities->all() as $capability) {
            if ($this->run($checks, $capability, $virtualserver, $context)) {
                $connectionUnusable = true;

                break;
            }
        }

        return [
            'healthy' => ! collect($checks)->contains(fn (array $check) => ! $check['healthy']),
            'connection_unusable' => $connectionUnusable,
            'checks' => $checks,
            'channel_list' => $context['channel_list'],
        ];
    }

    /** @param array<int, array{label: string, healthy: bool, message: string, permissions: array<int, string>}> $checks */
    /** @param array<string, mixed> $context */
    private function run(array &$checks, TeamSpeakCapability $capability, Server $server, array &$context): bool
    {
        try {
            $capability->probe($server, $context);
            $checks[] = $this->success($capability->label, $capability->permissions);

            return false;
        } catch (Throwable $exception) {
            $checks[] = $this->failure($capability->label, $exception, $capability->permissions);

            return TeamSpeakConnectionFailure::makesSessionUnusable($exception);
        }
    }

    /** @return array{label: string, healthy: bool, message: string, permissions: array<int, string>} */
    private function success(string $label, array $permissions = []): array
    {
        return ['label' => $label, 'healthy' => true, 'message' => 'OK', 'permissions' => $permissions];
    }

    /** @return array{label: string, healthy: bool, message: string, permissions: array<int, string>} */
    private function failure(string $label, Throwable $exception, array $permissions = []): array
    {
        return [
            'label' => $label,
            'healthy' => false,
            'message' => $this->formatFailure($exception->getMessage(), $permissions),
            'permissions' => $permissions,
        ];
    }

    /** @param array<int, string> $permissions */
    private function formatFailure(string $message, array $permissions): string
    {
        if ($permissions === [] || ! str_contains(strtolower($message), 'insufficient client permissions')) {
            return $message;
        }

        return $message.' Required ServerQuery permission(s): '.implode(', ', $permissions).'.';
    }

    private function formatRuntimeFailure(string $message): string
    {
        if (! str_contains(strtolower($message), 'insufficient client permissions') || str_contains($message, 'failed on b_')) {
            return $message;
        }

        $permission = $this->capabilities->permissionForQueryCommand($message);
        if (! is_null($permission)) {
            return $message." Required ServerQuery permission: $permission.";
        }

        return $message;
    }

    private function hasUnresolvedRuntimeError(Instance $instance): bool
    {
        return ! is_null($instance->health_last_runtime_error_at)
            && (is_null($instance->health_last_success_at)
                || ! $instance->health_last_success_at->greaterThan($instance->health_last_runtime_error_at));
    }
}
