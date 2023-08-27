<?php

namespace App\Http\Requests;

use App\Models\BannerTemplate;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
        try {
            $banner_template = BannerTemplate::findOrFail($this->banner_template_id);
        } catch (ModelNotFoundException) {
            // The below rule for `banner_template_id` will fail and handle this exception.
            // However, the other rules need a defined variable with the respective accessible properties,
            // so we simply return the first banner template.
            $banner_template = BannerTemplate::first();
        }

        return [
            'banner_template_id' => ['required', 'integer', 'exists:App\Models\BannerTemplate,id'],
            'redirect_url' => ['nullable', 'url'],
            'disable_at' => ['nullable', 'date'],
            'configuration' => ['required', 'array:banner_configuration_id,x_coordinate,y_coordinate,text,fontfile_path,font_size,font_angle,font_color_in_hexadecimal'],

            'configuration.banner_configuration_id' => ['sometimes', 'array'],
            'configuration.banner_configuration_id.*' => ['integer', 'exists:App\Models\BannerConfiguration,id'],

            'configuration.x_coordinate' => ['required', 'array', 'min:1'],
            'configuration.x_coordinate.*' => ['integer', 'min:0', 'max:'.$banner_template->template->width],

            'configuration.y_coordinate' => ['required', 'array', 'min:1'],
            'configuration.y_coordinate.*' => ['integer', 'min:0', 'max:'.$banner_template->template->height],

            'configuration.text.*' => ['required', 'array', 'min:1'],
            'configuration.text.*' => ['string', 'max:255'],

            'configuration.fontfile_path' => ['required', 'array', 'min:1'],
            'configuration.fontfile_path.*' => ['string'],

            'configuration.font_size' => ['required', 'array', 'min:1'],
            'configuration.font_size.*' => ['integer', 'min:1', 'max:'.$banner_template->template->height],

            'configuration.font_angle' => ['required', 'array', 'min:1'],
            'configuration.font_angle.*' => ['integer', 'min:0', 'max:360'],

            'configuration.font_color_in_hexadecimal' => ['required', 'array', 'min:1'],
            'configuration.font_color_in_hexadecimal.*' => ['string', 'regex:/^#[a-f0-9]{6}$/i'],
        ];
    }
}
