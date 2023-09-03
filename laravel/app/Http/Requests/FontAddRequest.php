<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FontAddRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'filename' => $this->file->getClientOriginalName(),
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
            'file' => ['required', 'file', 'mimetypes:font/ttf,font/sfnt'],
            'filename' => ['unique:fonts'],
        ];
    }
}
