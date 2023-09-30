<div class="row" id="optionInputGroup">
    @if (isset($configuration))
        <input type="hidden" name="configuration[banner_configuration_id][]" value="{{ $configuration->id }}">
    @endif
    <div class="col-lg-12 mb-3">
        <div class="row">
            <div class="col-lg-2">
                @if (isset($configuration))
                    <a class="form-control btn btn-danger btn-sm" href="{{ route('banner.template.configuration.delete', ['banner_configuration_id' => $configuration->id]) }}">{{ __('views/inc/banners/banner-config-input.delete_row_button') }}</a>
                @else
                    <button type="button" class="form-control btn btn-danger btn-sm" id="remove-config-row">{{ __('views/inc/banners/banner-config-input.remove_row_button') }}</button>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-2">
        <label for="validationXCoordinate" class="form-label">{{ __('views/inc/banners/banner-config-input.x_coordinate') }}</label>
        <input class="form-control" id="validationXCoordinate" type="number" min="0" step="1" max="{{ $template->width }}"
               name="configuration[x_coordinate][]"
               value="{{ old('configuration[x_coordinate][]', (isset($configuration)) ? $configuration->x_coordinate : '') }}"
               aria-describedby="xcoordinateHelp validationXCoordinateFeedback"
               placeholder="{{ __('views/inc/banners/banner-config-input.x_coordinate_placeholder') }}" required>
        <div id="xcoordinateHelp" class="form-text">{{ __('views/inc/banners/banner-config-input.x_coordinate_help') }}</div>
        <div class="valid-feedback">{{ __('views/inc/banners/banner-config-input.form_validation_looks_good') }}</div>
        <div id="validationXCoordinateFeedback" class="invalid-feedback">{{ __('views/inc/banners/banner-config-input.x_coordinate_validation_error') }}</div>
    </div>

    <div class="col-lg-4 mb-2">
        <label for="validationYCoordinate" class="form-label">{{ __('views/inc/banners/banner-config-input.y_coordinate') }}</label>
        <input class="form-control" id="validationYCoordinate" type="number" min="0" step="1" max="{{ $template->height }}"
               name="configuration[y_coordinate][]" value="{{ old('configuration[y_coordinate][]', (isset($configuration)) ? $configuration->y_coordinate : '') }}"
               aria-describedby="ycoordinateHelp validationYCoordinateFeedback"
               placeholder="{{ __('views/inc/banners/banner-config-input.y_coordinate_placeholder') }}" required>
        <div id="ycoordinateHelp" class="form-text">{{ __('views/inc/banners/banner-config-input.y_coordinate_help') }}</div>
        <div class="valid-feedback">{{ __('views/inc/banners/banner-config-input.form_validation_looks_good') }}</div>
        <div id="validationYCoordinateFeedback" class="invalid-feedback">Please{{ __('views/inc/banners/banner-config-input.y_coordinate_validation_error') }}</div>
    </div>

    <div class="col-lg-4 mb-2">
        <label for="validationText" class="form-label">{{ __('views/inc/banners/banner-config-input.text') }}</label>
        <input class="form-control" id="validationText" type="text"
               name="configuration[text][]" value="{{ old('configuration[text][]', (isset($configuration)) ? $configuration->text : '') }}"
               aria-describedby="textHelp validationTextFeedback"
               placeholder="{{ __('views/inc/banners/banner-config-input.text_placeholder') }}" required>
        <div id="textHelp" class="form-text">{{ __('views/inc/banners/banner-config-input.text_help') }}</div>
        <div class="valid-feedback">{{ __('views/inc/banners/banner-config-input.form_validation_looks_good') }}</div>
        <div id="validationTextFeedback" class="invalid-feedback">{{ __('views/inc/banners/banner-config-input.text_validation_error') }}</div>
    </div>

    <div class="col-lg-4 mb-2">
        <label for="validationFont" class="form-label">{{ __('views/inc/banners/banner-config-input.font_id') }}</label>
        <select class="form-select" name="configuration[font_id][]" id="validationFont" aria-describedby="FontHelp validationFontFeedback" required>
        @foreach ($fonts as $font)
            @if (isset($configuration) && $configuration->font->id == $font->id)
                <option value="{{ $font->id }}" selected>{{ $font->filename }}</option>
            @else
                <option value="{{ $font->id }}">{{ $font->filename }}</option>
            @endif
        @endforeach
        </select>
        <div id="FontHelp" class="form-text">{{ __('views/inc/banners/banner-config-input.font_id_help') }}</div>
        <div class="valid-feedback">{{ __('views/inc/banners/banner-config-input.form_validation_looks_good') }}</div>
        <div id="validationFontFeedback" class="invalid-feedback">{{ __('views/inc/banners/banner-config-input.font_id_validation_error') }}</div>
    </div>

    <div class="col-lg-4 mb-2">
        <label for="validationFontSize" class="form-label">{{ __('views/inc/banners/banner-config-input.font_size') }}</label>
        <input class="form-control" id="validationFontSize" type="number" min="1" step="1" max="{{ $banner_template->template->height }}"
               name="configuration[font_size][]" value="{{ old('configuration[font_size][]', (isset($configuration)) ? $configuration->font_size : 25) }}"
               aria-describedby="fontSizeHelp validationFontSizeFeedback"
               placeholder="{{ __('views/inc/banners/banner-config-input.font_size_placeholder') }}" required>
        <div id="fontSizeHelp" class="form-text">{{ __('views/inc/banners/banner-config-input.font_size_help') }}</div>
        <div class="valid-feedback">{{ __('views/inc/banners/banner-config-input.form_validation_looks_good') }}</div>
        <div id="validationFontSizeFeedback" class="invalid-feedback">{{ __('views/inc/banners/banner-config-input.font_size_validation_error') }}</div>
    </div>

    <div class="col-lg-4 mb-2">
        <label for="validationFontAngle" class="form-label">{{ __('views/inc/banners/banner-config-input.font_angle') }}</label>
        <input class="form-control" id="validationFontAngle" type="number" min="0" step="1" max="360"
               name="configuration[font_angle][]"
               value="{{ old('configuration[font_angle][]', (isset($configuration)) ? $configuration->font_angle : 0) }}"
               aria-describedby="fontAngleHelp validationFontAngleFeedback"
               placeholder="{{ __('views/inc/banners/banner-config-input.font_angle_placeholder') }}" required>
        <div id="fontAngleHelp" class="form-text">{{ __('views/inc/banners/banner-config-input.font_angle_help') }}</div>
        <div class="valid-feedback">{{ __('views/inc/banners/banner-config-input.form_validation_looks_good') }}</div>
        <div id="validationFontAngleFeedback" class="invalid-feedback">{{ __('views/inc/banners/banner-config-input.font_angle_validation_error') }}</div>
    </div>

    <div class="col-lg-4 mb-2">
        <label for="validationFontColorInHexadecimal" class="form-label">{{ __('views/inc/banners/banner-config-input.font_color_in_hexadecimal') }}</label>
        <input class="form-control" id="validationFontColorInHexadecimal" type="color"
               name="configuration[font_color_in_hexadecimal][]"
               value="{{ old('configuration[font_color_in_hexadecimal][]', (isset($configuration)) ? $configuration->font_color_in_hexadecimal : '#000000') }}"
               aria-describedby="colorHelp validationFontColorInHexadecimalFeedback"
               required>
        <div id="colorHelp" class="form-text">{{ __('views/inc/banners/banner-config-input.font_color_in_hexadecimal_help') }}</div>
        <div class="valid-feedback">{{ __('views/inc/banners/banner-config-input.form_validation_looks_good') }}</div>
        <div id="validationFontColorInHexadecimalFeedback" class="invalid-feedback">{{ __('views/inc/banners/banner-config-input.font_color_in_hexadecimal_validation_error') }}</div>
    </div>
</div>
<hr>
