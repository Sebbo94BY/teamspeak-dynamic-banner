<?php

namespace App\Console\Commands\Twitch;

use App\Models\TwitchApi;
use App\Models\TwitchStreamer;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class UpdateStreamerInformation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twitch:update-streamer-information';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieves current Twitch stream information and updates those in the system.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $twitch_api = TwitchApi::first();

        if (is_null($twitch_api)) {
            return $this->info('No Twitch API credentials have been provided yet. Doing nothing.');
        }

        $twitch_streamer = TwitchStreamer::all();

        $this->info('Retrieving current Twitch stream information for '.$twitch_streamer->count().' streams...');

        // Twitch accepts up to 100 `login` or `user_id` values per request. Keep
        // batches independent, so a failed request only leaves its own batch unchanged.
        foreach ($twitch_streamer->chunk(100) as $streamer_batch) {
            $streamers_by_login = $streamer_batch->keyBy(fn (TwitchStreamer $streamer) => strtolower(str_replace('https://www.twitch.tv/', '', $streamer->stream_url)));
            $users_response = $this->twitch_get($twitch_api, 'users', 'login', $streamers_by_login->keys()->all());

            if (! $users_response->successful()) {
                $this->warn('The Twitch API request failed due to the following error: '.json_encode($users_response->json()));
                continue;
            }

            $users_by_login = collect($users_response->json('data', []))->keyBy(fn (array $user) => strtolower($user['login']));
            foreach ($streamers_by_login as $login => $streamer) {
                if (! $users_by_login->has($login)) {
                    $this->info('Could not find any Twitch streamer with the stream URL '.$streamer->stream_url.'.');
                    $streamer->fill(['is_live' => false, 'started_at' => null, 'game_name' => null, 'title' => null, 'viewer_count' => 0]);
                    if ($streamer->isDirty()) {
                        $streamer->save();
                    }
                }
            }

            $streamers_by_user_id = $users_by_login->mapWithKeys(fn (array $user) => isset($streamers_by_login[strtolower($user['login'])]) ? [$user['id'] => $streamers_by_login[strtolower($user['login'])]] : []);
            if ($streamers_by_user_id->isEmpty()) {
                continue;
            }

            $streams_response = $this->twitch_get($twitch_api, 'streams', 'user_id', $streamers_by_user_id->keys()->all());
            if (! $streams_response->successful()) {
                $this->warn('The Twitch API request failed due to the following error: '.json_encode($streams_response->json()));
                continue;
            }

            $streams_by_user_id = collect($streams_response->json('data', []))->keyBy('user_id');
            foreach ($streamers_by_user_id as $user_id => $streamer) {
                $stream = $streams_by_user_id->get($user_id);
                if (is_null($stream) || $stream['type'] !== 'live') {
                    $this->info('The Twitch streamer '.$streamer->stream_url.' is currently offline.');
                    $streamer->is_live = false;
                } else {
                    $this->info('The Twitch streamer '.$streamer->stream_url.' is currently online.');
                    $streamer->fill([
                        'is_live' => true,
                        'started_at' => Carbon::parse($stream['started_at'])->setTimezone(config('app.timezone')),
                        'game_name' => $stream['game_name'],
                        'title' => $stream['title'],
                        'viewer_count' => (int) $stream['viewer_count'],
                    ]);
                }

                if ($streamer->isDirty()) {
                    $streamer->save();
                }
            }
        }
    }

    /** Build Twitch's repeated query parameter format without URL interpolation. */
    protected function twitch_get(TwitchApi $twitch_api, string $endpoint, string $parameter, array $values): Response
    {
        $query = implode('&', array_map(
            fn (string|int $value) => $parameter.'='.rawurlencode((string) $value),
            $values
        ));

        return Http::withHeaders(['Client-Id' => $twitch_api->client_id])
            ->withToken($twitch_api->access_token)
            ->timeout(10)
            ->get('https://api.twitch.tv/helix/'.$endpoint.'?'.$query);
    }
}
