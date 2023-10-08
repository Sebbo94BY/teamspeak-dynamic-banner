<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TwitchStreamerUpdateRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'twitch_streamer_id' => $this->route('twitch_streamer_id'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'twitch_streamer_id' => ['required', 'integer', 'exists:App\Models\TwitchStreamer,id'],
            'stream_url' => ['required', 'url:https', 'starts_with:https://www.twitch.tv/'],
        ];
    }
}
