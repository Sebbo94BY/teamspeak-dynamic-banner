<?php

namespace App\Http\Controllers\Administration;

use App\Http\Controllers\Controller;
use App\Http\Requests\TwitchApiCredentialsUpdateRequest;
use App\Http\Requests\TwitchStreamerAddRequest;
use App\Http\Requests\TwitchStreamerDeleteRequest;
use App\Http\Requests\TwitchStreamerUpdateRequest;
use App\Models\TwitchApi;
use App\Models\TwitchStreamer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class TwitchController extends Controller
{
    /**
     * Display the view.
     */
    public function view(): View
    {
        return view('administration.twitch')->with([
            'twitch_api' => TwitchApi::first(),
            'twitch_streamer' => TwitchStreamer::all(),
        ]);
    }

    /**
     * Updates the Twitch API credentials in the database.
     */
    public function update_api_credentials(TwitchApiCredentialsUpdateRequest $request): RedirectResponse
    {
        $twitch_api_response = Http::post('https://id.twitch.tv/oauth2/token?client_id='.$request->validated('client_id').'&client_secret='.$request->validated('client_secret').'&grant_type=client_credentials');

        if ($twitch_api_response->failed()) {
            return Redirect::route('administration.twitch')
                ->withInput($request->all())
                ->with([
                    'error' => 'twitch-api-credentials-update-error',
                    'message' => 'Failed to get an access token with the provided Twitch API credentials. Please double-check the API credentials! API Response: '.json_encode($twitch_api_response->json()),
                ]);
        }

        $twitch_api = TwitchApi::firstOrCreate();

        $twitch_api->client_id = $request->validated('client_id');
        $twitch_api->client_secret = $request->validated('client_secret');

        $twitch_api_response_json = $twitch_api_response->json();
        $twitch_api->access_token = $twitch_api_response_json['access_token'];
        $twitch_api->expires_at = Carbon::now()->addSeconds($twitch_api_response_json['expires_in']);

        if (! $twitch_api->save()) {
            return Redirect::route('administration.twitch')
                ->withInput($request->all())
                ->with([
                    'error' => 'twitch-api-credentials-update-error',
                    'message' => 'Failed to update the Twitch API credentials in the database. Please try again.',
                ]);
        }

        return Redirect::route('administration.twitch')->with([
            'success' => 'twitch-api-credentials-update-successful',
            'message' => 'Successfully updated the Twitch API credentials.',
        ]);
    }

    /**
     * Deletes the Twitch API credentials from the database.
     */
    public function delete_api_credentials(): RedirectResponse
    {
        $twitch_api = TwitchApi::first();

        if (! $twitch_api->delete()) {
            return Redirect::route('administration.twitch')
                ->with([
                    'error' => 'twitch-api-credentials-delete-error',
                    'message' => 'Failed to delete the Twitch API credentials from the database. Please try again.',
                ]);
        }

        return Redirect::route('administration.twitch')->with([
            'success' => 'twitch-api-credentials-delete-successful',
            'message' => 'Successfully deleted the Twitch API credentials.',
        ]);
    }

    /**
     * Adds a new Twitch streamer URL to the system.
     */
    public function create_streamer(TwitchStreamerAddRequest $request): RedirectResponse
    {
        $twitch_streamer = new TwitchStreamer();
        $twitch_streamer->stream_url = $request->validated('stream_url');

        if (! $twitch_streamer->save()) {
            return Redirect::route('administration.twitch')->withInput($request->all())->with([
                'error' => 'twitch-streamer-add-error',
                'message' => 'Failed to add the new Twitch streamer to the database. Please try again.',
            ]);
        }

        return Redirect::route('administration.twitch')->with([
            'success' => 'twitch-streamer-add-successful',
            'message' => 'Successfully added the new Twitch streamer.',
        ]);
    }

    /**
     * Updates an existing Twitch streamer URL in the system.
     */
    public function update_streamer(TwitchStreamerUpdateRequest $request): RedirectResponse
    {
        $twitch_streamer = TwitchStreamer::find($request->validated('twitch_streamer_id'));
        $twitch_streamer->stream_url = $request->validated('stream_url');

        if (! $twitch_streamer->save()) {
            return Redirect::route('administration.twitch')->withInput($request->all())->with([
                'error' => 'twitch-streamer-update-error',
                'message' => 'Failed to update the existing Twitch streamer in the database. Please try again.',
            ]);
        }

        return Redirect::route('administration.twitch')->with([
            'success' => 'twitch-streamer-update-successful',
            'message' => 'Successfully updated the Twitch streamer.',
        ]);
    }

    /**
     * Deletes an existing Twitch streamer URL from the system.
     */
    public function delete_streamer(TwitchStreamerDeleteRequest $request): RedirectResponse
    {
        $twitch_streamer = TwitchStreamer::find($request->validated('twitch_streamer_id'));

        if (! $twitch_streamer->delete()) {
            return Redirect::route('administration.twitch')->withInput($request->all())->with([
                'error' => 'twitch-streamer-delete-error',
                'message' => 'Failed to delete the existing Twitch streamer from the database. Please try again.',
            ]);
        }

        return Redirect::route('administration.twitch')->with([
            'success' => 'twitch-streamer-delete-successful',
            'message' => 'Successfully deleted the Twitch streamer.',
        ]);
    }
}
