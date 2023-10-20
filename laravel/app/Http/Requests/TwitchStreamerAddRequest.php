<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TwitchStreamerAddRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'stream_url' => ['required', 'url:https', 'starts_with:https://www.twitch.tv/'],
        ];
    }
}
