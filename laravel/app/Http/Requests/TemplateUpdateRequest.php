<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TemplateUpdateRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'template_id' => $this->route('template_id'),
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
            'template_id' => ['required', 'integer', 'exists:App\Models\Template,id'],
            'alias' => ['required', 'string'],
            'file' => ['nullable', 'image', 'mimes:png,jpg,jpeg,gif', 'dimensions:min_width=468,min_height=60,max_width=1024,max_height=300', 'max:5120'],
        ];
    }
}
