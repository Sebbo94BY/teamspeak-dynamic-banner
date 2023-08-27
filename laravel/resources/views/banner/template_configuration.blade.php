<div class="col-md-12 row">
    @if (isset($configuration))
    <input type="hidden" name="configuration[banner_configuration_id][]" value="{{ $configuration->id }}">
    @endif

    <div class="col-md-4">
        <label for="validationXCoordinate" class="form-label">X-Coordinate (horizontal)</label>
        <input class="form-control" id="validationXCoordinate" type="number" min="0" step="1" max="{{ $template->width }}" name="configuration[x_coordinate][]" value="{{ old('configuration[x_coordinate][]', (isset($configuration)) ? $configuration->x_coordinate : '') }}" placeholder="30" aria-describedby="xcoordinateHelp" required>
        <div id="xcoordinateHelp" class="form-text">The X-Coordinate, at which position the text should start.</div>
        <div class="valid-feedback">{{ __("Looks good!") }}</div>
        <div class="invalid-feedback">{{ __("Please provide a valid X-Coordinate.") }}</div>
    </div>

    <div class="col-md-4">
        <label for="validationYCoordinate" class="form-label">Y-Coordinate (vertical)</label>
        <input class="form-control" id="validationYCoordinate" type="number" min="0" step="1" max="{{ $template->height }}" name="configuration[y_coordinate][]" value="{{ old('configuration[y_coordinate][]', (isset($configuration)) ? $configuration->y_coordinate : '') }}" placeholder="60" aria-describedby="ycoordinateHelp" required>
        <div id="ycoordinateHelp" class="form-text">The Y-Coordinate, at which position the text should start.</div>
        <div class="valid-feedback">{{ __("Looks good!") }}</div>
        <div class="invalid-feedback">{{ __("Please provide a valid Y-Coordinate.") }}</div>
    </div>

    <div class="col-md-4">
        <label for="validationText" class="form-label">Text</label>
        <input class="form-control" id="validationText" type="text" name="configuration[text][]" value="{{ old('configuration[text][]', (isset($configuration)) ? $configuration->text : '') }}" placeholder="e.g. %VIRTUALSERVER_TOTAL_PING% ms" aria-describedby="textHelp" required>
        <div id="textHelp" class="form-text">The text, which should get printed to the image.</div>
        <div class="valid-feedback">{{ __("Looks good!") }}</div>
        <div class="invalid-feedback">{{ __("Please provide a valid text.") }}</div>
    </div>

    <div class="col-md-4">
        <label for="validationFont" class="form-label">Font</label>
        <select class="form-select" name="configuration[fontfile_path][]" id="validationFont" aria-describedby="FontHelp" required>
            @foreach ($installed_ttf_files as $fontfile_path)
            @if (isset($configuration) AND $configuration->fontfile_path == $fontfile_path)
            <option value="{{ $fontfile_path }}" selected>{{ preg_replace("/fonts\//", '', $fontfile_path) }}</option>
            @else
            <option value="{{ $fontfile_path }}">{{ preg_replace("/fonts\//", '', $fontfile_path) }}</option>
            @endif
            @endforeach
        </select>
        <div id="FontHelp" class="form-text">Please select the font for this specific text.</div>
        <div class="valid-feedback">{{ __("Looks good!") }}</div>
        <div class="invalid-feedback">{{ __("Please provide a valid and existing font (path).") }}</div>
    </div>

    <div class="col-md-4">
        <label for="validationFontSize" class="form-label">Font Size</label>
        <input class="form-control" id="validationFontSize" type="number" min="1" step="1" max="{{ $banner_template->template->height }}" name="configuration[font_size][]" value="{{ old('configuration[font_size][]', (isset($configuration)) ? $configuration->font_size : 25) }}" placeholder="e.g. 5" aria-describedby="fontSizeHelp" required>
        <div id="fontSizeHelp" class="form-text">The font size of the text, which should get printed to the image.</div>
        <div class="valid-feedback">{{ __("Looks good!") }}</div>
        <div class="invalid-feedback">{{ __("Please provide a valid font size.") }}</div>
    </div>

    <div class="col-md-4">
        <label for="validationFontAngle" class="form-label">Font Angle</label>
        <input class="form-control" id="validationFontAngle" type="number" min="0" step="1" max="360" name="configuration[font_angle][]" value="{{ old('configuration[font_angle][]', (isset($configuration)) ? $configuration->font_angle : 0) }}" placeholder="e.g. 0" aria-describedby="fontAngleHelp" required>
        <div id="fontAngleHelp" class="form-text">The font angle between 0 and 360 degree in which the text should get printed to the image.</div>
        <div class="valid-feedback">{{ __("Looks good!") }}</div>
        <div class="invalid-feedback">{{ __("Please provide a valid font angle between 0 and 360 degree.") }}</div>
    </div>

    <div class="col-md-4">
        <label for="validationFontColorInHexadecimal" class="form-label">Text Color</label>
        <input class="form-control" id="validationFontColorInHexadecimal" type="color" name="configuration[font_color_in_hexadecimal][]" value="{{ old('configuration[font_color_in_hexadecimal][]', (isset($configuration)) ? $configuration->font_color_in_hexadecimal : '#000000') }}" aria-describedby="FontColorInHexadecimalHelp" required>
        <div id="FontColorInHexadecimalHelp" class="form-text">Define the text color in which your text should be printed.</div>
        <div class="valid-feedback">{{ __("Looks good!") }}</div>
        <div class="invalid-feedback">{{ __("Please provide a valid hexadecimal color code.") }}</div>
    </div>

    <div class="col-md-2">
        @if (isset($configuration))
        <a class="form-control btn btn-danger" href="{{ route('banner.template.configuration.delete', ['banner_configuration_id' => $configuration->id]) }}">{{ __('Delete') }}</a>
        @else
        <label for="remove-config-row" class="form-label"></label>
        <button type="button" class="form-control btn btn-danger" id="remove-config-row">Remove row</button>
        @endif
    </div>
</div>
