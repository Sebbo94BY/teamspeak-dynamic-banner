<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BannerUpdateRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'banner_id' => $this->route('banner_id'),
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
            'banner_id' => ['required', 'integer', 'exists:App\Models\Banner,id'],
            'name' => ['required', 'string', 'max:255'],
            'instance_id' => ['required', 'integer', 'exists:App\Models\Instance,id'],
            'random_rotation' => ['sometimes'],
        ];
    }
}
