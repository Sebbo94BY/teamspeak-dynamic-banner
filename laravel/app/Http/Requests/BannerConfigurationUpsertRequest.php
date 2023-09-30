<?php

namespace App\Http\Requests;

use App\Models\BannerTemplate;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;

class BannerConfigurationUpsertRequest extends FormRequest
{
    /**
     * The banner template, which should get upserted with this request.
     */
    protected BannerTemplate $banner_template;

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'banner_template_id' => $this->route('banner_template_id'),
        ]);

        try {
            $this->banner_template = BannerTemplate::findOrFail($this->banner_template_id);
        } catch (ModelNotFoundException) {
            // The below rule for `banner_template_id` will fail and handle this exception.
            // However, the other rules need a defined variable with the respective accessible properties,
            // so we simply return the first banner template.
            $this->banner_template = BannerTemplate::first();
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'banner_template_id' => ['required', 'integer', 'exists:App\Models\BannerTemplate,id'],
            'name' => ['required', 'string'],
            'redirect_url' => ['nullable', 'url'],
            'disable_at' => ['nullable', 'date'],
            'time_based_enable_at' => ['nullable', 'date_format:H:i,H:i:s'],
            'time_based_disable_at' => ['nullable', 'date_format:H:i,H:i:s'],
            'configuration' => ['required', 'array:banner_configuration_id,x_coordinate,y_coordinate,text,font_id,font_size,font_angle,font_color_in_hexadecimal'],

            'configuration.banner_configuration_id' => ['sometimes', 'array'],
            'configuration.banner_configuration_id.*' => ['integer', 'exists:App\Models\BannerConfiguration,id'],

            'configuration.x_coordinate' => ['required', 'array', 'min:1'],
            'configuration.x_coordinate.*' => ['integer', 'min:0', 'max:'.$this->banner_template->template->width],

            'configuration.y_coordinate' => ['required', 'array', 'min:1'],
            'configuration.y_coordinate.*' => ['integer', 'min:0', 'max:'.$this->banner_template->template->height],

            'configuration.text' => ['required', 'array', 'min:1'],
            'configuration.text.*' => ['string', 'max:255'],

            'configuration.font_id' => ['required', 'array', 'min:1'],
            'configuration.font_id.*' => ['integer', 'exists:App\Models\Font,id'],

            'configuration.font_size' => ['required', 'array', 'min:1'],
            'configuration.font_size.*' => ['integer', 'min:1', 'max:'.$this->banner_template->template->height],

            'configuration.font_angle' => ['required', 'array', 'min:1'],
            'configuration.font_angle.*' => ['integer', 'min:0', 'max:360'],

            'configuration.font_color_in_hexadecimal' => ['required', 'array', 'min:1'],
            'configuration.font_color_in_hexadecimal.*' => ['string', 'regex:/^#[a-f0-9]{6}$/i'],
        ];
    }
}
