<?php

namespace App\Http\Requests;

use App\Models\BannerTemplate;
use Illuminate\Foundation\Http\FormRequest;

class BannerConfigurationUpsertRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $banner_template = BannerTemplate::findOrFail($this->banner_template_id);

        return [
            'redirect_url' => ['nullable', 'url'],
            'banner_configuration_id.*' => ['sometimes', 'integer', 'exists:App\Models\BannerConfiguration,id'],
            'banner_template_id' => ['sometimes', 'integer', 'exists:App\Models\BannerTemplate,id'],
            'x_coordinate.*' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:'.$banner_template->template->width],
            'y_coordinate.*' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:'.$banner_template->template->height],
            'text.*' => ['sometimes', 'nullable', 'string', 'max:255'],
            'fontfile_path.*' => ['sometimes', 'string'],
            'font_size.*' => ['sometimes', 'integer', 'min:1', 'max:'.$banner_template->template->height],
            'font_angle.*' => ['sometimes', 'integer', 'min:0', 'max:360'],
            'font_color_in_hexadecimal.*' => ['sometimes', 'string', 'regex:/^#[a-f0-9]{6}$/i'],
        ];
    }
}
