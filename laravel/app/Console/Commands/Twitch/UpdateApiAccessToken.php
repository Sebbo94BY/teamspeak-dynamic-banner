<?php

namespace App\Console\Commands\Twitch;

use App\Models\TwitchApi;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class UpdateApiAccessToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'twitch:update-api-access-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Performs a Twitch API authentication and updates the access token in the database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $twitch_api = TwitchApi::first();

        if (is_null($twitch_api)) {
            return $this->info('No twitch API has been configured yet. Skipping.');
        }

        if (! is_null($twitch_api->access_token) and Carbon::now()->addMinutes(30)->lt($twitch_api->expires_at)) {
            return $this->info('The existing access token is still valid for at least the next 30 minutes. Skipping.');
        }

        $this->info('Performing a Twitch API authentication...');

        $twitch_api_response = Http::post('https://id.twitch.tv/oauth2/token?client_id='.$twitch_api->client_id.'&client_secret='.$twitch_api->client_secret.'&grant_type=client_credentials');

        if ($twitch_api_response->failed()) {
            return $this->error('The Twitch API authentication request failed due to the following error: '.json_encode($twitch_api_response->json()));
        }

        $twitch_api_response_json = $twitch_api_response->json();

        $twitch_api->access_token = $twitch_api_response_json['access_token'];
        $twitch_api->expires_at = Carbon::now()->addSeconds($twitch_api_response_json['expires_in']);

        if (! $twitch_api->save()) {
            return $this->error('Failed to update the access token and expiry date in the database.');
        }

        $this->info('Successfully updated the access token and expiry date in the database.');
    }
}
