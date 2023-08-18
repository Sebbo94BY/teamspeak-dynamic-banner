@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="col-md-12 row">
                        <div class="col-md-6">
                            {{ __("Edit the banner configuration.") }}

                            <a href="{{ route('banner.variables', ['banner_id' => $banner_template->banner->id]) }}" target="_blank" class="btn btn-info">{{ __("Available Variables") }}</a>
                        </div>

                        <div class="col-md-1 offset-md-5">
                            @if ($banner_template->enabled)
                                <form class="d-flex flex-row align-items-center flex-wrap" method="POST" action="{{ route('banner.template.disable') }}" novalidate>
                                    @method('patch')
                                    @csrf
                                    <input type="hidden" name="banner_id" value="{{ $banner_template->banner_id }}">
                                    <input type="hidden" name="template_id" value="{{ $banner_template->template_id }}">

                                    <span class="badge" data-bs-toggle="tooltip" data-bs-html="true"
                                        title="Disable this configuration."
                                        id="disable-configuration-badge">
                                        <button type="submit" class="btn btn-info"><i class="fa-solid fa-toggle-on"></i></button>
                                    </span>
                                </form>
                            @else
                                <form class="d-flex flex-row align-items-center flex-wrap" method="POST" action="{{ route('banner.template.enable') }}" novalidate>
                                    @method('patch')
                                    @csrf
                                    <input type="hidden" name="banner_id" value="{{ $banner_template->banner_id }}">
                                    <input type="hidden" name="template_id" value="{{ $banner_template->template_id }}">

                                    <span class="badge" data-bs-toggle="tooltip" data-bs-html="true"
                                        title="Enable this configuration."
                                        id="disable-configuration-badge">
                                        <button type="submit" class="btn btn-dark"><i class="fa-solid fa-toggle-off"></i></button>
                                    </span>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('message') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('message') }}
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="col-md-12 row">
                        <div class="col-md-6">
                            <label for="templateWithGrid" class="form-label">Template with Grid System</label>
                            <img id="templateWithGrid" class="img-fluid shadow-lg p-1 mb-2 bg-white rounded" src="{{ asset($banner_template->template->file_path_drawed_grid_text.'/'.$banner_template->template->filename) }}" alt="{{ $banner_template->template->alias }}">
                        </div>

                        <div class="col-md-6">
                            <label for="renderedTemplate" class="form-label">Rendered Template Preview</label>
                            <img id="renderedTemplate" class="img-fluid shadow-lg p-1 mb-2 bg-white rounded" src="{{ asset($banner_template->template->file_path_drawed_text.'/'.$banner_template->template->filename) }}" alt="{{ $banner_template->template->alias }}">
                        </div>
                    </div>

                    <hr>

                    <div class="col-md-12 row">
                        <p><b>About the Grid System</b></p>
                        <p>{{ __('With the help of the grid system, you are able to faster identify, which X-Y-Coordinates you need to put your text at the correct position.') }}</p>
                        <p>{{ __('Each horizontal and vertical line of the grid system represents 25px. In the left top corner, X and Y is 0px.') }}</p>
                        <p>{{ __('Click on the position in the image to get the X-Y-Coordinates: ') }}</p>
                        <div class="col-md-3">
                            <label for="XCoordinatePreview" class="form-label">X-Coordinate (horizontal)</label>
                            <input class="form-control" id="XCoordinatePreview" type="number" min="0" step="1" max="{{ $banner_template->template->width }}" name="x_coordinate_preview" value="0" disabled>
                        </div>
                        <div class="col-md-3">
                            <label for="YCoordinatePreview" class="form-label">Y-Coordinate (vertical)</label>
                            <input class="form-control" id="YCoordinatePreview" type="number" min="0" step="1" max="{{ $banner_template->template->height }}" name="y_coordinate_preview" value="0" disabled>
                        </div>
                    </div>

                    <hr>

                    <form id="template-configuration" method="POST" action="{{ route('banner.template.configuration.upsert', ['banner_id' => $banner_template->banner_id, 'template_id' => $banner_template->template_id]) }}" class="row g-3 needs-validation" novalidate>
                        @method('patch')
                        @csrf
                        <input type="hidden" name="banner_template_id" value="{{ $banner_template->id }}">

                        <div class="col-md-12">
                            <label for="validationRedirectUrl" class="form-label">Redirect URL</label>
                            <div class="input-group">
                                <input class="form-control" id="validationRedirectUrl" type="url" name="redirect_url" value="{{ old('redirect_url', (isset($banner_template)) ? $banner_template->redirect_url : '') }}" placeholder="e.g. https://example.com/news" aria-describedby="redirectUrlHelp">
                                <a href="{{ route('api.banner.redirect_url', ['banner_id' => base_convert($banner_template->banner_id, 10, 35), 'banner_template_id' => $banner_template->id]) }}" target="_blank" class="btn btn-primary">Test Redirect</a>
                            </div>
                            <div id="redirectUrlHelp" class="form-text">An optional URL, where the user should get redirected, when he clicks on the banner. By default, the rendered template will be opened.</div>
                            <div class="valid-feedback">{{ __("Looks good!") }}</div>
                            <div class="invalid-feedback">{{ __("Please provide a valid URL.") }}</div>
                        </div>

                        <p>For this functionality you need to configure the following URL as Hostbanner URL on your TeamSpeak server: <code>{{ route('api.banner.redirect_url', ['banner_id' => base_convert($banner_template->banner_id, 10, 35)]) }}</code></p>

                        <hr>

                        @if ($banner_template->configurations->count() > 0)
                            @foreach ($banner_template->configurations as $configuration)
                                <div id="config-row-{{ $configuration->id }}">
                                    @include('banner.template_configuration', ['template' => $banner_template->template, 'configuration' => $configuration])
                                    <hr>
                                </div>
                            @endforeach
                            @php unset($configuration) @endphp

                            <div id="new-config-row-1" class="d-none">
                                @include('banner.template_configuration', ['template' => $banner_template->template])
                                <hr>
                            </div>
                        @else
                            <div id="new-config-row-1">
                                @include('banner.template_configuration', ['template' => $banner_template->template])
                                <hr>
                            </div>
                        @endif

                        <div class="mb-3">
                            <a href="{{ route('banners') }}" class="btn btn-secondary">Cancel</a>
                            <button type="button" class="btn btn-success" id="add-config-row">Add row</button>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="module">
    // Enable Bootstrap Tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // Get X-Y-Coordinates when clicking on the image
    $(document).ready(function() {
        $('img').click(function(e) {
            var offset = $(this).offset();

            var x_padding_offset = $(this).innerWidth() - $(this).width();
            var x_coordinate = ((e.pageX - offset.left) * (this.naturalWidth / $(this).width())) - x_padding_offset;

            $('input[name=x_coordinate_preview]').val(Math.floor(x_coordinate));

            var y_padding_offset = $(this).innerHeight() - $(this).height();
            var y_coordinate = ((e.pageY - offset.top) * (this.naturalHeight / $(this).height())) - y_padding_offset;

            $('input[name=y_coordinate_preview]').val(Math.floor(y_coordinate));
        });
    });

    // On page load, remove the `required` attributes for the hidden DIV to avoid
    // issues by the form validation when it's not filled out.
    $(document).ready(function(){
        $('div.d-none > div > div > input').each(function() {
            $(this).prop("required", false);
        });
    });

    // Example starter JavaScript for disabling form submissions if there are invalid fields
    (function () {
    'use strict'

    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.querySelectorAll('.needs-validation')

    // Loop over them and prevent submission
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
            }

            form.classList.add('was-validated')
        }, false)
        })
    })();

    // Add additional configuration row, if requested by the user
    $("#add-config-row").click(function () {
        // Find the last DIV with the ID "new-config-row-<NUMBER>"
        var $config_row = $('[id^="new-config-row"]:last');

        if ($config_row.prop("class").match(/d-none/g)) {
            // Add the `required` attribute to enforce the form validation
            $('div.d-none > div > div > input').each(function() {
                $(this).prop("required", true);
            });

            // Unhide the row
            $config_row.removeClass("d-none");
        } else {
            // Otherwise clone the DIV
            // Get the NUMBER from the DIV and increment it by one
            var next_number = parseInt($config_row.prop("id").match(/\d+/g), 10) + 1;

            // Clone the last DIV and replace the ID to make it unique
            var $clone = $config_row.clone().prop('id', 'new-config-row-' + next_number);

            // Insert the cloned DIV at the end
            $config_row.after($clone);
        }
    });

    // Remove existing, but not yet saved configuration row, if requested by the user
    $(document).on('click', '#remove-config-row', function () {
        $(this).closest('[id^="new-config-row"]').remove();
    })
</script>
@endsection
