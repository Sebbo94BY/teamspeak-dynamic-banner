<div class="row" id="optionInputGroup">
    @if (isset($configuration))
        <input type="hidden" name="configuration[banner_configuration_id][]" value="{{ $configuration->id }}">
    @endif
    <div class="col-lg-12 mb-3">
        <div class="row">
            <div class="col-lg-2">
                @if (isset($configuration))
                <a class="form-control btn btn-danger btn-sm" href="{{ route('banner.template.configuration.delete', ['banner_configuration_id' => $configuration->id]) }}">Delete</a>
                @else
                    <button type="button" class="form-control btn btn-danger btn-sm" id="remove-config-row">Remove row</button>
                @endif
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-2">
        <label for="validationXCoordinate" class="form-label">X-Coordinate (horizontal)</label>
        <input class="form-control" id="validationXCoordinate" type="number" min="0" step="1" max="{{ $template->width }}"
               name="configuration[x_coordinate][]"
               value="{{ old('configuration[x_coordinate][]', (isset($configuration)) ? $configuration->x_coordinate : '') }}"
               aria-describedby="xcoordinateHelp validationXCoordinateFeedback"
               placeholder="e.g. 30" required>
        <div id="xcoordinateHelp" class="form-text">The X-Coordinate, at which position the text should start.</div>
        <div class="valid-feedback">Looks good!</div>
        <div id="validationXCoordinateFeedback" class="invalid-feedback">Please provide a valid X-Coordinate.</div>
    </div>

    <div class="col-lg-4 mb-2">
        <label for="validationYCoordinate" class="form-label">Y-Coordinate (vertical)</label>
        <input class="form-control" id="validationYCoordinate" type="number" min="0" step="1" max="{{ $template->height }}"
               name="configuration[y_coordinate][]" value="{{ old('configuration[y_coordinate][]', (isset($configuration)) ? $configuration->y_coordinate : '') }}"
               aria-describedby="ycoordinateHelp validationYCoordinateFeedback"
               placeholder="e.g. 60" required>
        <div id="ycoordinateHelp" class="form-text">The Y-Coordinate, at which position the text should start.</div>
        <div class="valid-feedback">Looks good!</div>
        <div id="validationYCoordinateFeedback" class="invalid-feedback">Please provide a valid Y-Coordinate.</div>
    </div>

    <div class="col-lg-4 mb-2">
        <label for="validationText" class="form-label">Text</label>
        <input class="form-control" id="validationText" type="text"
               name="configuration[text][]" value="{{ old('configuration[text][]', (isset($configuration)) ? $configuration->text : '') }}"
               aria-describedby="textHelp validationTextFeedback"
               placeholder="e.g. %VIRTUALSERVER_TOTAL_PING% ms" required>
        <div id="textHelp" class="form-text">The text, which should get printed to the image.</div>
        <div class="valid-feedback">Looks good!</div>
        <div id="validationTextFeedback" class="invalid-feedback">Please provide a valid text.</div>
    </div>

    <div class="col-lg-4 mb-2">
        <label for="validationFont" class="form-label">Font</label>
        <select class="form-select" name="configuration[font_id][]" id="validationFont" aria-describedby="FontHelp validationFontFeedback" required>
        @foreach ($fonts as $font)
            @if (isset($configuration) && $configuration->font->id == $font->id)
                <option value="{{ $font->id }}" selected>{{ $font->filename }}</option>
            @else
                <option value="{{ $font->id }}">{{ $font->filename }}</option>
            @endif
        @endforeach
        </select>
        <div id="FontHelp" class="form-text">Please select the font for this specific text.</div>
        <div class="valid-feedback">Looks good!</div>
        <div id="validationFontFeedback" class="invalid-feedback">Please provide a valid and existing font (path).</div>
    </div>

    <div class="col-lg-4 mb-2">
        <label for="validationFontSize" class="form-label">Font Size</label>
        <input class="form-control" id="validationFontSize" type="number" min="1" step="1" max="{{ $banner_template->template->height }}"
               name="configuration[font_size][]" value="{{ old('configuration[font_size][]', (isset($configuration)) ? $configuration->font_size : 25) }}"
               aria-describedby="fontSizeHelp validationFontSizeFeedback"
               placeholder="e.g. 5" required>
        <div id="fontSizeHelp" class="form-text">The font size of the text, which should get printed to the image.</div>
        <div class="valid-feedback">Looks good!</div>
        <div id="validationFontSizeFeedback" class="invalid-feedback">Please provide a valid font size.</div>
    </div>

    <div class="col-lg-4 mb-2">
        <label for="validationFontAngle" class="form-label">Font Angle</label>
        <input class="form-control" id="validationFontAngle" type="number" min="0" step="1" max="360"
               name="configuration[font_angle][]"
               value="{{ old('configuration[font_angle][]', (isset($configuration)) ? $configuration->font_angle : 0) }}"
               aria-describedby="fontAngleHelp validationFontAngleFeedback"
               placeholder="e.g. 0" required>
        <div id="fontAngleHelp" class="form-text">The font angle between 0 and 360 degree in which the text should get printed to the image.</div>
        <div class="valid-feedback">Looks good!</div>
        <div id="validationFontAngleFeedback" class="invalid-feedback">Please provide a valid font angle between 0 and 360 degree.</div>
    </div>

    <div class="col-lg-4 mb-2">
        <label for="validationFontColorInHexadecimal" class="form-label">Text Color</label>
        <input class="form-control" id="validationFontColorInHexadecimal" type="color"
               name="configuration[font_color_in_hexadecimal][]"
               value="{{ old('configuration[font_color_in_hexadecimal][]', (isset($configuration)) ? $configuration->font_color_in_hexadecimal : '#000000') }}"
               aria-describedby="colorHelp validationFontColorInHexadecimalFeedback"
               required>
        <div id="colorHelp" class="form-text">Define the text color in which your text should be printed.</div>
        <div class="valid-feedback">Looks good!</div>
        <div id="validationFontColorInHexadecimalFeedback" class="invalid-feedback">Please provide a valid hexadecimal color code.</div>
    </div>
</div>
<hr>
