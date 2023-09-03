<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BannerTemplateRemoveRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'banner_template_id' => $this->route('banner_template_id'),
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
            'banner_template_id' => ['required', 'integer', 'exists:App\Models\BannerTemplate,id'],
        ];
    }
}
