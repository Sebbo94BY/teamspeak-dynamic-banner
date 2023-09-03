<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BannerConfigurationDeleteRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'banner_configuration_id' => $this->route('banner_configuration_id'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'banner_configuration_id' => ['required', 'integer', 'exists:App\Models\BannerConfiguration,id'],
        ];
    }
}
