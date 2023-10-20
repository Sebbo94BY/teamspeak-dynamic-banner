<?php

namespace App\Console\Commands\Twitch;

use App\Models\TwitchApi;
use App\Models\TwitchStreamer;
use Carbon\Carbon;
use Illuminate\Console\Command;
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

        foreach ($twitch_streamer as $streamer) {
            $streamer_login = str_replace('https://www.twitch.tv/', '', $streamer->stream_url);

            $twitch_api_users_response = Http::withHeaders([
                'Client-Id' => $twitch_api->client_id,
            ])->withToken($twitch_api->access_token)->get("https://api.twitch.tv/helix/users?login=$streamer_login");

            if ($twitch_api_users_response->failed()) {
                $this->warn('The Twitch API request failed due to the following error: '.json_encode($twitch_api_users_response->json()));
                continue;
            }

            $twitch_api_users_response_json = $twitch_api_users_response->json();

            if (count($twitch_api_users_response_json['data']) == 0) {
                $this->info('Could not find any Twitch streamer with the stream URL '.$streamer->stream_url.'.');

                $streamer->is_live = false;
                $streamer->started_at = null;
                $streamer->game_name = null;
                $streamer->title = null;
                $streamer->viewer_count = 0;
                $streamer->save();

                continue;
            }

            $twitch_api_streams_response = Http::withHeaders([
                'Client-Id' => $twitch_api->client_id,
            ])->withToken($twitch_api->access_token)->get('https://api.twitch.tv/helix/streams?user_id='.reset($twitch_api_users_response_json['data'])['id']);

            $twitch_api_streams_response_json = $twitch_api_streams_response->json();

            if (count($twitch_api_streams_response_json['data']) == 0 or (reset($twitch_api_streams_response_json['data'])['type'] != 'live')) {
                $this->info('The Twitch streamer '.$streamer->stream_url.' is currently offline.');

                $streamer->is_live = false;
                $streamer->save();

                continue;
            }

            $this->info('The Twitch streamer '.$streamer->stream_url.' is currently online.');

            $twitch_api_streams_response_json = reset($twitch_api_streams_response_json['data']);

            $streamer->is_live = true;
            $streamer->started_at = Carbon::parse($twitch_api_streams_response_json['started_at'])->setTimezone(config('app.timezone'));
            $streamer->game_name = $twitch_api_streams_response_json['game_name'];
            $streamer->title = $twitch_api_streams_response_json['title'];
            $streamer->viewer_count = intval($twitch_api_streams_response_json['viewer_count']);
            $streamer->save();
        }
    }
}
