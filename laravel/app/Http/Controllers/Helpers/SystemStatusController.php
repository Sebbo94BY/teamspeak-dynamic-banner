<?php

namespace App\Http\Controllers\Helpers;

use App\Http\Controllers\Controller;
use App\Models\QueueMetricSnapshot;
use App\Support\QueueWorkerHeartbeat;
use Exception;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Predis\PredisException;
use RedisException;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

/**
 * Possible system status severities.
 */
enum SystemStatusSeverity: string
{
    case Info = 'info';
    case Success = 'success';
    case Warning = 'warning';
    case Danger = 'danger';
}

class SystemStatusController extends Controller
{
    public const QUEUE_METRIC_HISTORY_RANGES = [
        '30m' => 30,
        '6h' => 360,
        '1d' => 1440,
        '7d' => 10080,
        '30d' => 43200,
    ];

    /**
     * Checks PHP version.
     */
    protected function check_php_version(): array
    {
        $requirements = [];

        $requirements['VERSION_ID']['name'] = __('views/inc/system/systemstatus.accordion_section_php_version');
        $requirements['VERSION_ID']['current_value'] = PHP_VERSION_ID.' ('.PHP_VERSION.')';
        $requirements['VERSION_ID']['required_value'] = '>= 80200 (8.2.0)';
        $requirements['VERSION_ID']['severity'] = (PHP_VERSION_ID >= 80200) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        return $requirements;
    }

    /**
     * Checks PHP extensions.
     */
    protected function check_php_extensions(): array
    {
        $requirements = [];

        /**
         * Laravel specific requirements
         */
        $requirements['CTYPE']['name'] = 'ctype';
        $requirements['CTYPE']['severity'] = (extension_loaded('ctype')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['CURL']['name'] = 'curl';
        $requirements['CURL']['severity'] = (extension_loaded('curl')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['DOM']['name'] = 'dom';
        $requirements['DOM']['severity'] = (extension_loaded('dom')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['FILEINFO']['name'] = 'fileinfo';
        $requirements['FILEINFO']['severity'] = (extension_loaded('fileinfo')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['FILTER']['name'] = 'filter';
        $requirements['FILTER']['severity'] = (extension_loaded('filter')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['HASH']['name'] = 'hash';
        $requirements['HASH']['severity'] = (extension_loaded('hash')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['MBSTRING']['name'] = 'mbstring';
        $requirements['MBSTRING']['severity'] = (extension_loaded('mbstring')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['OPENSSL']['name'] = 'openssl';
        $requirements['OPENSSL']['severity'] = (extension_loaded('openssl')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['PCRE']['name'] = 'pcre';
        $requirements['PCRE']['severity'] = (extension_loaded('openssl')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['PDO']['name'] = 'pdo';
        $requirements['PDO']['severity'] = (extension_loaded('pdo')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['SESSION']['name'] = 'session';
        $requirements['SESSION']['severity'] = (extension_loaded('session')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['TOKENIZER']['name'] = 'tokenizer';
        $requirements['TOKENIZER']['severity'] = (extension_loaded('tokenizer')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['XML']['name'] = 'xml';
        $requirements['XML']['severity'] = (extension_loaded('xml')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['PDO_MYSQL']['name'] = 'pdo_mysql';
        $requirements['PDO_MYSQL']['severity'] = (extension_loaded('pdo_mysql')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        /**
         * Project specific requirements
         */
        $requirements['SSH2']['name'] = 'ssh2';
        $requirements['SSH2']['severity'] = (extension_loaded('ssh2')) ? SystemStatusSeverity::Success : SystemStatusSeverity::Warning;

        $requirements['GD_PNG_SUPPORT']['name'] = 'gd (PNG Support)';
        $requirements['GD_PNG_SUPPORT']['severity'] = ((extension_loaded('gd')) ?? gd_info()['PNG Support']) ? SystemStatusSeverity::Success : SystemStatusSeverity::Warning;

        $requirements['GD_JPEG_SUPPORT']['name'] = 'gd (JPEG Support)';
        $requirements['GD_JPEG_SUPPORT']['severity'] = ((extension_loaded('gd')) ?? gd_info()['JPEG Support']) ? SystemStatusSeverity::Success : SystemStatusSeverity::Warning;

        return $requirements;
    }

    /**
     * Checks PHP INI setings.
     */
    protected function check_php_ini_settings(): array
    {
        $disable_functions = ini_get('disable_functions');
        $requirements['DISABLE_FUNCTIONS']['name'] = 'PHP disable_functions';
        $requirements['DISABLE_FUNCTIONS']['current_value'] = (empty(trim($disable_functions))) ? __('views/inc/system/systemstatus.accordion_section_php_ini_disable_functions_current_value_empty_list') : $disable_functions;
        $requirements['DISABLE_FUNCTIONS']['required_value'] = __('views/inc/system/systemstatus.accordion_section_php_ini_disable_functions_required_value');
        $requirements['DISABLE_FUNCTIONS']['severity'] = (preg_match('/shell_exec/', $disable_functions)) ? SystemStatusSeverity::Warning : SystemStatusSeverity::Success;

        $max_execution_time = ini_get('max_execution_time');
        $requirements['MAX_EXECUTION_TIME']['name'] = 'PHP max_execution_time';
        $requirements['MAX_EXECUTION_TIME']['current_value'] = $max_execution_time;
        $requirements['MAX_EXECUTION_TIME']['required_value'] = '0, -1 OR >=30';
        $requirements['MAX_EXECUTION_TIME']['severity'] = (
            ($max_execution_time == 0) or
            ($max_execution_time == '-1') or
            ($max_execution_time >= 0)
        ) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $memory_limit = ini_get('memory_limit');
        $requirements['MEMORY_LIMIT']['name'] = 'PHP memory_limit';
        $requirements['MEMORY_LIMIT']['current_value'] = $memory_limit;
        $requirements['MEMORY_LIMIT']['required_value'] = '>= 128M';
        $requirements['MEMORY_LIMIT']['severity'] = (substr($memory_limit, 0, -1) >= 128) ? SystemStatusSeverity::Success : SystemStatusSeverity::Warning;

        $upload_max_filesize = ini_get('upload_max_filesize');
        $requirements['UPLOAD_MAX_FILESIZE']['name'] = 'PHP upload_max_filesize';
        $requirements['UPLOAD_MAX_FILESIZE']['current_value'] = $upload_max_filesize;
        $requirements['UPLOAD_MAX_FILESIZE']['required_value'] = '>= 5M';
        $requirements['UPLOAD_MAX_FILESIZE']['severity'] = (substr($upload_max_filesize, 0, -1) * 1024 * 1024 >= 5 * 1024 * 1024) ? SystemStatusSeverity::Success : SystemStatusSeverity::Warning;

        $post_max_size = ini_get('post_max_size');
        $requirements['POST_MAX_SIZE']['name'] = 'PHP post_max_size';
        $requirements['POST_MAX_SIZE']['current_value'] = $post_max_size;
        $requirements['POST_MAX_SIZE']['required_value'] = '>= upload_max_filesize';
        $requirements['POST_MAX_SIZE']['severity'] = (substr($post_max_size, 0, -1) * 1024 * 1024 >= substr($upload_max_filesize, 0, -1) * 1024 * 1024) ? SystemStatusSeverity::Success : SystemStatusSeverity::Warning;

        $date_timezone = ini_get('date.timezone');
        $requirements['DATE_TIMEZONE']['name'] = 'PHP date.timezone';
        $requirements['DATE_TIMEZONE']['current_value'] = $date_timezone;
        $requirements['DATE_TIMEZONE']['required_value'] = __('views/inc/system/systemstatus.accordion_section_php_ini_date_timezone_required_value');
        $requirements['DATE_TIMEZONE']['severity'] = (! empty(trim($date_timezone))) ? SystemStatusSeverity::Success : SystemStatusSeverity::Warning;

        return $requirements;
    }

    /**
     * Checks database connection.
     */
    protected function check_database_connection(): array
    {
        $requirements = [];

        try {
            $db_name = DB::connection()->getDatabaseName();
        } catch (Exception $exception) {
            $db_name = $exception;
        }

        $requirements['TEST']['name'] = __('views/inc/system/systemstatus.accordion_section_database_connection');
        $requirements['TEST']['current_value'] = (is_string($db_name)) ? __('views/inc/system/systemstatus.accordion_section_database_connection_current_value_connected') : __('views/inc/system/systemstatus.accordion_section_database_connection_current_value_error', ['exception' => $db_name]);
        $requirements['TEST']['required_value'] = __('views/inc/system/systemstatus.accordion_section_database_connection_required_value');
        $requirements['TEST']['severity'] = (is_string($db_name)) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        return $requirements;
    }

    /**
     * Checks database settings.
     */
    protected function check_database_settings(): array
    {
        $requirements = [];

        try {
            $db_name = DB::connection()->getDatabaseName();
        } catch (Exception $exception) {
            $db_name = $exception->getMessage();
        }

        $requirements['DB_NAME']['name'] = __('views/inc/system/systemstatus.accordion_section_database_name');
        $requirements['DB_NAME']['current_value'] = $db_name;
        $requirements['DB_NAME']['required_value'] = null;
        $requirements['DB_NAME']['severity'] = (is_string($db_name)) ? SystemStatusSeverity::Info : SystemStatusSeverity::Danger;

        $username = config('database.connections.'.Config::get('database.default').'.username');
        $requirements['DB_USER']['name'] = __('views/inc/system/systemstatus.accordion_section_database_user');
        $requirements['DB_USER']['current_value'] = $username;
        $requirements['DB_USER']['required_value'] = __('views/inc/system/systemstatus.accordion_section_database_user_required_value');
        $requirements['DB_USER']['severity'] = ($username != 'root') ? SystemStatusSeverity::Info : SystemStatusSeverity::Warning;

        $charset = config('database.connections.'.Config::get('database.default').'.charset');
        $requirements['CHARACTER_SET']['name'] = __('views/inc/system/systemstatus.accordion_section_database_character_set');
        $requirements['CHARACTER_SET']['current_value'] = $charset;
        $requirements['CHARACTER_SET']['required_value'] = __('views/inc/system/systemstatus.accordion_section_database_character_set_required_value');
        $requirements['CHARACTER_SET']['severity'] = (preg_match('/^utf8/', $charset)) ? SystemStatusSeverity::Success : SystemStatusSeverity::Warning;

        $collation = config('database.connections.'.Config::get('database.default').'.collation');
        $requirements['COLLATION']['name'] = __('views/inc/system/systemstatus.accordion_section_database_collation');
        $requirements['COLLATION']['current_value'] = $collation;
        $requirements['COLLATION']['required_value'] = __('views/inc/system/systemstatus.accordion_section_database_collation_required_value');
        $requirements['COLLATION']['severity'] = (preg_match('/^utf8/', $collation)) ? SystemStatusSeverity::Success : SystemStatusSeverity::Warning;

        return $requirements;
    }

    /**
     * Checks directories.
     */
    protected function check_directories(): array
    {
        $requirements = [];

        $requirements['STORAGE_FRAMEWORK_DIR']['name'] = storage_path('framework');
        $requirements['STORAGE_FRAMEWORK_DIR']['severity'] = (is_writable(storage_path('framework'))) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['STORAGE_LOGS_DIR']['name'] = storage_path('logs');
        $requirements['STORAGE_LOGS_DIR']['severity'] = (is_writable(storage_path('logs'))) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        $requirements['PUBLIC_DIR']['name'] = public_path();
        $requirements['PUBLIC_DIR']['severity'] = (is_writable(public_path())) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        return $requirements;
    }

    /**
     * Checks Queue health.
     */
    protected function check_queue_health(string $queue_name): array
    {
        $requirements = [];

        $active_workers = app(QueueWorkerHeartbeat::class)->active_workers($queue_name);
        $requirements['WORKERS']['name'] = __('views/inc/system/systemstatus.accordion_section_queue_health_workers');
        $requirements['WORKERS']['current_value'] = is_null($active_workers)
            ? __('views/inc/system/systemstatus.accordion_section_queue_health_workers_unavailable')
            : trans_choice('views/inc/system/systemstatus.accordion_section_queue_health_workers_current_value', $active_workers, ['count' => $active_workers]);
        $requirements['WORKERS']['required_value'] = is_null($active_workers)
            ? __('views/inc/system/systemstatus.accordion_section_queue_health_workers_unavailable_action')
            : ($active_workers > 0
                ? __('views/inc/system/systemstatus.accordion_section_queue_health_workers_required_value')
                : __('views/inc/system/systemstatus.accordion_section_queue_health_workers_missing_action'));
        $requirements['WORKERS']['severity'] = is_null($active_workers)
            ? SystemStatusSeverity::Info
            : ($active_workers > 0 ? SystemStatusSeverity::Success : SystemStatusSeverity::Warning);

        $recent_throughput = $this->recent_queue_throughput($queue_name);
        $requirements['THROUGHPUT']['name'] = __('views/inc/system/systemstatus.accordion_section_queue_health_throughput');
        $requirements['THROUGHPUT']['history_key'] = 'processed';
        $requirements['THROUGHPUT']['current_value'] = trans_choice('views/inc/system/systemstatus.accordion_section_queue_health_throughput_current_value', $recent_throughput, ['count' => $recent_throughput]);
        $requirements['THROUGHPUT']['required_value'] = __('views/inc/system/systemstatus.accordion_section_queue_health_throughput_required_value');
        $requirements['THROUGHPUT']['severity'] = SystemStatusSeverity::Info;

        $requirements['SIZE']['name'] = __('views/inc/system/systemstatus.accordion_section_queue_health_size');
        $requirements['SIZE']['history_key'] = 'total';
        $requirements['SIZE']['required_value'] = __('views/inc/system/systemstatus.accordion_section_queue_health_size_required_value');
        $requirements['SIZE']['severity'] = SystemStatusSeverity::Info;

        $queue_connection = config('queue.default');
        $queue_configuration = config('queue.connections.'.$queue_connection);

        if (($queue_configuration['driver'] ?? null) !== 'database') {
            $requirements['SIZE']['current_value'] = Queue::connection()->size($queue_name);
            $requirements['OLDEST_JOB']['name'] = __('views/inc/system/systemstatus.accordion_section_queue_health_oldest_job');
            $requirements['OLDEST_JOB']['current_value'] = __('views/inc/system/systemstatus.accordion_section_queue_health_oldest_job_unavailable');
            $requirements['OLDEST_JOB']['required_value'] = __('views/inc/system/systemstatus.accordion_section_queue_health_oldest_job_unavailable_action');
            $requirements['OLDEST_JOB']['severity'] = SystemStatusSeverity::Info;

            return $requirements;
        }

        $now = now()->timestamp;
        $retry_after = $queue_configuration['retry_after'] ?? 90;
        $queue_jobs = DB::table($queue_configuration['table'])->where('queue', $queue_name);
        $ready_jobs = (clone $queue_jobs)
            ->whereNull('reserved_at')
            ->where('available_at', '<=', $now);
        $processing_jobs = (clone $queue_jobs)
            ->whereNotNull('reserved_at')
            ->where('reserved_at', '>=', $now - $retry_after);
        $scheduled_jobs = (clone $queue_jobs)
            ->whereNull('reserved_at')
            ->where('available_at', '>', $now);
        $stale_jobs = (clone $queue_jobs)
            ->whereNotNull('reserved_at')
            ->where('reserved_at', '<', $now - $retry_after);

        $requirements['SIZE']['current_value'] = $queue_jobs->count();
        $requirements['SIZE']['required_value'] = __('views/inc/system/systemstatus.accordion_section_queue_health_size_required_value');
        $requirements['READY']['name'] = __('views/inc/system/systemstatus.accordion_section_queue_health_ready_jobs');
        $requirements['READY']['history_key'] = 'ready';
        $requirements['READY']['current_value'] = (clone $ready_jobs)->count();
        $requirements['READY']['required_value'] = __('views/inc/system/systemstatus.accordion_section_queue_health_ready_jobs_required_value');
        $requirements['READY']['severity'] = SystemStatusSeverity::Info;

        $requirements['PROCESSING']['name'] = __('views/inc/system/systemstatus.accordion_section_queue_health_processing_jobs');
        $requirements['PROCESSING']['history_key'] = 'processing';
        $requirements['PROCESSING']['current_value'] = $processing_jobs->count();
        $requirements['PROCESSING']['required_value'] = __('views/inc/system/systemstatus.accordion_section_queue_health_processing_jobs_required_value');
        $requirements['PROCESSING']['severity'] = SystemStatusSeverity::Info;

        $requirements['SCHEDULED']['name'] = __('views/inc/system/systemstatus.accordion_section_queue_health_scheduled_jobs');
        $requirements['SCHEDULED']['history_key'] = 'scheduled';
        $next_scheduled_job = (clone $scheduled_jobs)->orderBy('available_at')->first();
        $next_scheduled_job_in = $next_scheduled_job ? max(0, $next_scheduled_job->available_at - $now) : 0;
        $scheduled_job_count = $scheduled_jobs->count();
        $scheduled_jobs_are_unusually_delayed = $next_scheduled_job_in > 300;
        $requirements['SCHEDULED']['current_value'] = ($scheduled_job_count > 0)
            ? __('views/inc/system/systemstatus.accordion_section_queue_health_scheduled_jobs_current_value', ['count' => $scheduled_job_count, 'wait' => $this->format_queue_age($next_scheduled_job_in)])
            : 0;
        $requirements['SCHEDULED']['required_value'] = $scheduled_jobs_are_unusually_delayed
            ? __('views/inc/system/systemstatus.accordion_section_queue_health_scheduled_jobs_delayed_action')
            : __('views/inc/system/systemstatus.accordion_section_queue_health_scheduled_jobs_required_value');
        $requirements['SCHEDULED']['severity'] = $scheduled_jobs_are_unusually_delayed ? SystemStatusSeverity::Warning : SystemStatusSeverity::Info;

        $requirements['STALE']['name'] = __('views/inc/system/systemstatus.accordion_section_queue_health_stale_jobs');
        $requirements['STALE']['history_key'] = 'stale';
        $requirements['STALE']['current_value'] = $stale_jobs->count();
        $requirements['STALE']['required_value'] = __('views/inc/system/systemstatus.accordion_section_queue_health_stale_jobs_required_value');
        $requirements['STALE']['severity'] = ($requirements['STALE']['current_value'] > 0) ? SystemStatusSeverity::Warning : SystemStatusSeverity::Success;

        $oldest_job = $ready_jobs->orderBy('created_at')->first();

        $maximum_wait_seconds = 300;
        $oldest_job_age = $oldest_job ? max(0, now()->timestamp - $oldest_job->created_at) : 0;
        $queue_is_delayed = $oldest_job_age > $maximum_wait_seconds;

        $requirements['OLDEST_JOB']['name'] = __('views/inc/system/systemstatus.accordion_section_queue_health_oldest_job');
        $requirements['OLDEST_JOB']['history_key'] = 'oldest_wait_seconds';
        $requirements['OLDEST_JOB']['current_value'] = $oldest_job ? $this->format_queue_age($oldest_job_age) : __('views/inc/system/systemstatus.accordion_section_queue_health_oldest_job_empty');
        $requirements['OLDEST_JOB']['required_value'] = $queue_is_delayed
            ? __('views/inc/system/systemstatus.accordion_section_queue_health_oldest_job_delayed_action')
            : __('views/inc/system/systemstatus.accordion_section_queue_health_oldest_job_required_value', ['minutes' => intdiv($maximum_wait_seconds, 60)]);
        $requirements['OLDEST_JOB']['severity'] = $queue_is_delayed ? SystemStatusSeverity::Warning : SystemStatusSeverity::Success;

        $requirements['RECOMMENDATION']['name'] = __('views/inc/system/systemstatus.accordion_section_queue_health_recommendation');
        $requirements['RECOMMENDATION']['current_value'] = $this->queue_worker_recommendation($requirements['READY']['current_value'], $oldest_job_age, $active_workers, $recent_throughput);
        $requirements['RECOMMENDATION']['required_value'] = __('views/inc/system/systemstatus.accordion_section_queue_health_recommendation_required_value');
        $requirements['RECOMMENDATION']['severity'] = ($requirements['READY']['current_value'] > 0 && $oldest_job_age > $maximum_wait_seconds) ? SystemStatusSeverity::Warning : SystemStatusSeverity::Info;

        $priority = ['WORKERS' => 0, 'RECOMMENDATION' => 1];
        uksort($requirements, fn (string $left, string $right) => ($priority[$left] ?? 2) <=> ($priority[$right] ?? 2));

        return $requirements;
    }

    protected function recent_queue_throughput(string $queue_name): int
    {
        return (int) QueueMetricSnapshot::where('queue', $queue_name)
            ->where('recorded_at', '>=', now()->subMinutes(5))
            ->sum('processed');
    }

    protected function queue_worker_recommendation(int $ready_jobs, int $oldest_job_age, ?int $active_workers, int $recent_throughput): string
    {
        if ($ready_jobs === 0) {
            return __('views/inc/system/systemstatus.accordion_section_queue_health_recommendation_no_action');
        }

        if ($active_workers === 0) {
            return __('views/inc/system/systemstatus.accordion_section_queue_health_recommendation_start_worker');
        }

        if (is_null($active_workers) || $oldest_job_age <= 300 || $recent_throughput === 0) {
            return __('views/inc/system/systemstatus.accordion_section_queue_health_recommendation_observe');
        }

        $jobs_per_worker_per_minute = $recent_throughput / 5 / $active_workers;
        $additional_workers = (int) ceil(max(0, ($ready_jobs / 5 - $recent_throughput / 5) / $jobs_per_worker_per_minute));

        return $additional_workers > 0
            ? trans_choice('views/inc/system/systemstatus.accordion_section_queue_health_recommendation_add_workers', $additional_workers, ['count' => $additional_workers])
            : __('views/inc/system/systemstatus.accordion_section_queue_health_recommendation_no_action');
    }

    /**
     * Formats a queue waiting time in a concise, human-readable form.
     */
    protected function format_queue_age(int $seconds): string
    {
        if ($seconds < 60) {
            return trans_choice('views/inc/system/systemstatus.accordion_section_queue_health_age_seconds', $seconds, ['count' => $seconds]);
        }

        if ($seconds < 3600) {
            $minutes = intdiv($seconds, 60);

            return trans_choice('views/inc/system/systemstatus.accordion_section_queue_health_age_minutes', $minutes, ['count' => $minutes]);
        }

        $hours = intdiv($seconds, 3600);

        return trans_choice('views/inc/system/systemstatus.accordion_section_queue_health_age_hours', $hours, ['count' => $hours]);
    }

    /**
     * Returns the queue measurements from the last thirty minutes.
     */
    protected function queue_metric_history(string $range, string $queue_name)
    {
        $history = QueueMetricSnapshot::where('queue', $queue_name)
            ->where('recorded_at', '>=', now()->subMinutes(self::QUEUE_METRIC_HISTORY_RANGES[$range]))
            ->orderBy('recorded_at')
            ->get();

        $maximum_points = 240;
        if ($history->count() <= $maximum_points) {
            return $history;
        }

        $step = (int) ceil($history->count() / $maximum_points);
        $last_index = $history->count() - 1;

        return $history->filter(fn ($snapshot, $index) => $index % $step === 0 || $index === $last_index)->values();
    }

    /**
     * Checks Redis connection.
     */
    protected function check_redis_connection(): array
    {
        $requirements = [];

        $reachable = false;
        try {
            Redis::ping();
            $reachable = true;
        } catch (RedisException|PredisException $connection_exception) {
            $redis_connection_exception = $connection_exception->getMessage();
        }

        $requirements['TEST']['name'] = __('views/inc/system/systemstatus.accordion_section_redis_connection');
        $requirements['TEST']['current_value'] = ($reachable) ? __('views/inc/system/systemstatus.accordion_section_redis_connection_current_value_connected') : __('views/inc/system/systemstatus.accordion_section_redis_connection_current_value_error', ['exception' => $redis_connection_exception]);
        $requirements['TEST']['required_value'] = __('views/inc/system/systemstatus.accordion_section_redis_connection_required_value');
        $requirements['TEST']['severity'] = ($reachable) ? SystemStatusSeverity::Success : SystemStatusSeverity::Danger;

        return $requirements;
    }

    /**
     * Reports the configured Redis client and recommends the native PhpRedis extension.
     */
    protected function check_redis_client(): array
    {
        $client = config('database.redis.client');

        return [
            'CLIENT' => [
                'name' => __('views/inc/system/systemstatus.accordion_section_redis_client'),
                'current_value' => $client,
                'required_value' => __('views/inc/system/systemstatus.accordion_section_redis_client_required_value'),
                'severity' => ($client === 'phpredis') ? SystemStatusSeverity::Success : SystemStatusSeverity::Warning,
            ],
        ];
    }

    /**
     * Checks FFMpeg version.
     */
    protected function check_ffmpeg_version(): array
    {
        $requirements = [];

        $command_output = (preg_match('/shell_exec/', ini_get('disable_functions'))) ? null : @shell_exec('ffmpeg -version | head -1');

        $requirements['VERSION']['name'] = __('views/inc/system/systemstatus.accordion_section_ffmpeg_version');
        $requirements['VERSION']['current_value'] = (is_null($command_output) or empty(trim($command_output))) ? __('views/inc/system/systemstatus.accordion_section_ffmpeg_version_current_value_error') : $command_output;
        $requirements['VERSION']['required_value'] = __('views/inc/system/systemstatus.accordion_section_ffmpeg_version_required_value');
        $requirements['VERSION']['severity'] = (is_null($command_output) or empty(trim($command_output))) ? SystemStatusSeverity::Warning : SystemStatusSeverity::Success;

        return $requirements;
    }

    /**
     * Checks Mail connection.
     */
    protected function check_mail_connection(): array
    {
        $requirements = [];

        $requirements['TEST']['name'] = __('views/inc/system/systemstatus.accordion_section_mail_connection');
        $requirements['TEST']['required_value'] = __('views/inc/system/systemstatus.accordion_section_mail_connection_required_value');

        switch (config('mail.default')) {
            case 'smtp':
                $reachable = false;

                /**
                 * Yes, this is kinda confusing. To use TLS (STARTTLS) `$tls = false` needs to be passed to EsmtpTransport.
                 * Symfony tries by default to use (START)TLS then, if the server supports it.
                 *
                 * - https://github.com/symfony/mailer/blob/v6.3.5/Transport/Smtp/EsmtpTransport.php#L54-L66
                 * - https://github.com/symfony/mailer/blob/v6.3.5/Transport/Smtp/Stream/SocketStream.php#L138-L140
                 * - https://stackoverflow.com/questions/75712823/laravel-10-test-email-connection-with-esmtptransport-class
                 */
                switch (strtolower(config('mail.mailers.smtp.encryption'))) {
                    case 'tls' || 'starttls':
                        $tls = false;
                        break;

                    case 'ssl':
                        $tls = true;
                        break;

                    case '':
                        $tls = null;
                        break;

                    default:
                        $tls = null;
                        break;
                }

                try {
                    // create a new SMTP connection
                    $transport = new EsmtpTransport(config('mail.mailers.smtp.host'), config('mail.mailers.smtp.port'), $tls);

                    $stream = $transport->getStream();

                    // set timeout
                    $stream->setTimeout(config('mail.mailers.smtp.timeout'));

                    // set `verify_peer` based on the DotEnv configuration
                    $streamOptions = $stream->getStreamOptions();
                    $streamOptions['ssl']['verify_peer'] = config('mail.mailers.smtp.verify_peer');
                    $streamOptions['ssl']['verify_peer_name'] = config('mail.mailers.smtp.verify_peer');
                    $stream->setStreamOptions($streamOptions);

                    // set authentication
                    $transport->setUsername(config('mail.mailers.smtp.username') ?? '');
                    $transport->setPassword(config('mail.mailers.smtp.password') ?? '');

                    // connect
                    $transport->start();

                    // test connection (check if a ping succeeds)
                    $transport->executeCommand("NOOP\r\n", [250]);

                    // disconnect
                    $transport->stop();

                    $reachable = true;
                } catch (Exception $e) {
                    $mail_connection_exception = $e->getMessage();
                }

                $requirements['TEST']['current_value'] = ($reachable) ? __('views/inc/system/systemstatus.accordion_section_mail_connection_current_value_connected') : __('views/inc/system/systemstatus.accordion_section_mail_connection_current_value_error', ['exception' => $mail_connection_exception]);
                $requirements['TEST']['severity'] = ($reachable) ? SystemStatusSeverity::Success : SystemStatusSeverity::Warning;

                break;

            default:
                $requirements['TEST']['current_value'] = __('views/inc/system/systemstatus.accordion_section_mail_connection_current_value_unsupported_mailer_for_testing');
                $requirements['TEST']['severity'] = SystemStatusSeverity::Warning;
                break;
        }

        return $requirements;
    }

    /**
     * Checks versions.
     */
    protected function check_versions(): array
    {
        $requirements = [];

        $requirements['PHP_VERSION']['name'] = __('views/inc/system/systemstatus.accordion_section_version_php');
        $requirements['PHP_VERSION']['current_value'] = PHP_VERSION;
        $requirements['PHP_VERSION']['severity'] = SystemStatusSeverity::Info;

        $requirements['LARAVEL_VERSION']['name'] = __('views/inc/system/systemstatus.accordion_section_version_laravel');
        $requirements['LARAVEL_VERSION']['current_value'] = Application::VERSION;
        $requirements['LARAVEL_VERSION']['severity'] = SystemStatusSeverity::Info;

        return $requirements;
    }

    /**
     * Checks various information.
     */
    protected function check_various_information(): array
    {
        $requirements = [];

        $is_git_deployment = file_exists('../../.git/');

        $requirements['IS_GIT_DEPLOYMENT']['name'] = __('views/inc/system/systemstatus.accordion_section_various_git_deployment');
        $requirements['IS_GIT_DEPLOYMENT']['current_value'] = ($is_git_deployment) ? __('views/inc/system/systemstatus.accordion_section_various_git_deployment_current_value_yes') : __('views/inc/system/systemstatus.accordion_section_various_git_deployment_current_value_no');
        $requirements['IS_GIT_DEPLOYMENT']['required_value'] = null;
        $requirements['IS_GIT_DEPLOYMENT']['severity'] = SystemStatusSeverity::Info;

        $requirements['GIT_COMMIT_SHA']['name'] = __('views/inc/system/systemstatus.accordion_section_various_git_commit_sha');
        $requirements['GIT_COMMIT_SHA']['current_value'] = ($is_git_deployment) ? trim(exec('tail -1 ../../.git/logs/HEAD | cut -d " " -f 2')) : __('views/inc/system/systemstatus.accordion_section_various_git_commit_sha_unknown');
        $requirements['GIT_COMMIT_SHA']['required_value'] = null;
        $requirements['GIT_COMMIT_SHA']['severity'] = SystemStatusSeverity::Info;

        $requirements['APP_ENVIRONMENT']['name'] = __('views/inc/system/systemstatus.accordion_section_various_app_env');
        $requirements['APP_ENVIRONMENT']['current_value'] = Config::get('app.env');
        $requirements['APP_ENVIRONMENT']['required_value'] = __('views/inc/system/systemstatus.accordion_section_various_app_env_required_value');
        $requirements['APP_ENVIRONMENT']['severity'] = SystemStatusSeverity::Info;

        $requirements['APP_DEBUG']['name'] = __('views/inc/system/systemstatus.accordion_section_various_app_debug');
        $requirements['APP_DEBUG']['current_value'] = (Config::get('app.debug')) ? __('views/inc/system/systemstatus.accordion_section_various_app_debug_current_value_enabled') : __('views/inc/system/systemstatus.accordion_section_various_app_debug_current_value_disabled');
        $requirements['APP_DEBUG']['required_value'] = __('views/inc/system/systemstatus.accordion_section_various_app_debug_required_value');
        $requirements['APP_DEBUG']['severity'] = SystemStatusSeverity::Info;

        if (isset($_SERVER['SERVER_SOFTWARE'])) {
            $requirements['SERVER_SOFTWARE']['name'] = __('views/inc/system/systemstatus.accordion_section_various_server_software');
            $requirements['SERVER_SOFTWARE']['current_value'] = $_SERVER['SERVER_SOFTWARE'];
            $requirements['SERVER_SOFTWARE']['required_value'] = null;
            $requirements['SERVER_SOFTWARE']['severity'] = SystemStatusSeverity::Info;
        }

        $requirements['PHP_BINARY']['name'] = __('views/inc/system/systemstatus.accordion_section_various_php_binary');
        $requirements['PHP_BINARY']['current_value'] = PHP_BINARY;
        $requirements['PHP_BINARY']['required_value'] = null;
        $requirements['PHP_BINARY']['severity'] = SystemStatusSeverity::Info;

        return $requirements;
    }

    /**
     * Returns a summary of the system status in JSON format.
     */
    public function system_status_json($optional_information = true): array
    {
        $system_status = [];

        $system_status['PHP']['VERSION'] = $this->check_php_version();
        $system_status['PHP']['EXTENSIONS'] = $this->check_php_extensions();
        $system_status['PHP']['INI_SETTINGS'] = $this->check_php_ini_settings();
        $system_status['DATABASE']['CONNECTION'] = $this->check_database_connection();
        $system_status['DATABASE']['SETTINGS'] = $this->check_database_settings();
        $system_status['PERMISSIONS']['DIRECTORIES'] = $this->check_directories();
        $queue_connection = config('queue.default');
        $default_queue = config('queue.connections.'.$queue_connection.'.queue', 'default');
        $system_status['QUEUE']['HEALTH'] = $this->check_queue_health($default_queue);
        if (config('matomo.enabled')) {
            $system_status['QUEUE']['MATOMO'] = $this->check_queue_health('matomo');
        }
        $system_status['REDIS']['CONNECTION'] = $this->check_redis_connection();
        $system_status['REDIS']['CLIENT'] = $this->check_redis_client();
        $system_status['FFMPEG']['VERSION'] = $this->check_ffmpeg_version();
        $system_status['MAIL']['CONNECTION'] = $this->check_mail_connection();

        if ($optional_information) {
            $system_status['VERSIONS']['SOFTWARE'] = $this->check_versions();
            $system_status['VARIOUS']['INFORMATION'] = $this->check_various_information();
        }

        return $system_status;
    }

    public function system_status(string $queue_history_range = '30m'): array
    {
        if (! array_key_exists($queue_history_range, self::QUEUE_METRIC_HISTORY_RANGES)) {
            $queue_history_range = '30m';
        }

        $system_status = collect(json_decode(json_encode($this->system_status_json())));
        $php_status = collect($system_status['PHP']);
        $php_extensions = collect($php_status['EXTENSIONS']);
        $php_ini_settings = collect($php_status['INI_SETTINGS']);

        $db_status = collect($system_status['DATABASE']);
        $db_status_connection = collect($db_status['CONNECTION']);
        $db_status_settings = collect($db_status['SETTINGS']);

        $permission_status = collect($system_status['PERMISSIONS']);
        $permission_status_dir = collect($permission_status['DIRECTORIES']);

        $queue_status = collect($system_status['QUEUE']);
        $queue_health_size = collect($queue_status['HEALTH']);
        $queue_health_sections = [[
            'name' => __('views/inc/system/systemstatus.accordion_section_queue_health_default_queue'),
            'metrics' => $queue_health_size,
            'history' => $this->queue_metric_history($queue_history_range, config('queue.connections.'.config('queue.default').'.queue', 'default')),
        ]];
        if ($queue_status->has('MATOMO')) {
            $queue_health_sections[] = [
                'name' => __('views/inc/system/systemstatus.accordion_section_queue_health_matomo_queue'),
                'metrics' => collect($queue_status['MATOMO']),
                'history' => $this->queue_metric_history($queue_history_range, 'matomo'),
            ];
        }

        $redis_staus = collect($system_status['REDIS']);
        $redis_staus_connection = collect($redis_staus['CONNECTION'])
            ->merge(collect($redis_staus['CLIENT']));

        $ffmpeg_status = collect($system_status['FFMPEG']);
        $ffmpeg_status_version = collect($ffmpeg_status['VERSION']);

        $mail_status = collect($system_status['MAIL']);
        $mail_status_connection = collect($mail_status['CONNECTION']);

        $versions_status = collect($system_status['VERSIONS']);
        $versions_status_software = collect($versions_status['SOFTWARE']);

        $various_status = collect($system_status['VARIOUS']);
        $various_status_information = collect($various_status['INFORMATION']);

        return [
            'php_status' => $php_status,
            'php_status_extension' => $php_extensions,
            'php_status_ini_settings' => $php_ini_settings,
            'php_warning_count' => preg_match_all("/\"severity\"\:\"warning\"/", $php_status),
            'php_error_count' => preg_match_all("/\"severity\"\:\"danger\"/", $php_status),
            'db_status_connection' => $db_status_connection,
            'db_status_Settings' => $db_status_settings,
            'db_warning_count' => preg_match_all("/\"severity\"\:\"warning\"/", $db_status),
            'db_error_count' => preg_match_all("/\"severity\"\:\"danger\"/", $db_status),
            'permission_status_dir' => $permission_status_dir,
            'permission_warning_count' => preg_match_all("/\"severity\"\:\"warning\"/", $permission_status),
            'permission_error_count' => preg_match_all("/\"severity\"\:\"danger\"/", $permission_status),
            'queue_health_size' => $queue_health_size,
            'queue_health_sections' => $queue_health_sections,
            'queue_metric_history_ranges' => self::QUEUE_METRIC_HISTORY_RANGES,
            'queue_history_range' => $queue_history_range,
            'queue_health_warning_count' => preg_match_all("/\"severity\"\:\"warning\"/", $queue_status),
            'queue_health_error_count' => preg_match_all("/\"severity\"\:\"danger\"/", $queue_status),
            'redis_status_connection' => $redis_staus_connection,
            'redis_warning_count' => preg_match_all("/\"severity\"\:\"warning\"/", $redis_staus),
            'redis_error_count' => preg_match_all("/\"severity\"\:\"danger\"/", $redis_staus),
            'ffmpeg_version' => $ffmpeg_status_version,
            'ffmpeg_warning_count' => preg_match_all("/\"severity\"\:\"warning\"/", $ffmpeg_status),
            'ffmpeg_error_count' => preg_match_all("/\"severity\"\:\"danger\"/", $ffmpeg_status),
            'mail_status_connection' => $mail_status_connection,
            'mail_warning_count' => preg_match_all("/\"severity\"\:\"warning\"/", $mail_status),
            'mail_error_count' => preg_match_all("/\"severity\"\:\"danger\"/", $mail_status),
            'version_status_software' => $versions_status_software,
            'version_warning_count' => preg_match_all("/\"severity\"\:\"warning\"/", $versions_status),
            'version_error_count' => preg_match_all("/\"severity\"\:\"danger\"/", $versions_status),
            'various_status_information' => $various_status_information,
            'various_warning_count' => preg_match_all("/\"severity\"\:\"warning\"/", $various_status),
            'various_error_count' => preg_match_all("/\"severity\"\:\"danger\"/", $various_status),
            'system_status_warning_count' => preg_match_all("/\"severity\"\:\"warning\"/", $system_status),
            'system_status_danger_count' => preg_match_all("/\"severity\"\:\"danger\"/", $system_status),
        ];
    }
}
