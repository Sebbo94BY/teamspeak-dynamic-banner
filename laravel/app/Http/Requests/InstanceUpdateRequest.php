<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InstanceUpdateRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'instance_id' => $this->route('instance_id'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'instance_id' => ['required', 'integer', 'exists:App\Models\Instance,id'],
            'host' => ['required', 'string', 'max:255'],
            'voice_port' => ['required', 'integer', 'min:1', 'max:65535'],
            'serverquery_port' => ['required', 'integer', 'min:1', 'max:65535'],
            'is_ssh' => ['sometimes'],
            'serverquery_username' => ['required', 'string'],
            'serverquery_password' => ['required', 'string'],
            'client_nickname' => ['required', 'string', 'max:30'],
            'default_channel_id' => ['nullable', 'integer'],
            'autostart_enabled' => ['sometimes'],
        ];
    }
}
