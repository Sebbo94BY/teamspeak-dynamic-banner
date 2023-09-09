@extends('layout')

@section('site_title')
    Banner configuration
@endsection

@section('content')
<div class="container mt-3">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="fw-bold fs-3">Banner configuration for "{{$banner_template->banner->name}}"</h1>
        </div>
    </div>
    <hr>
</div>
<div class="container">
    <div class="row justify-content-between">
        <div class="col-lg-2">
            <a class="btn btn-primary btn-sm fw-bold" href="#modalAvailableVariables-{{$banner_template->banner->instance_id}}" data-bs-toggle="modal" data-bs-target="#modalAvailableVariables-{{$instance->first()->id}}"><i class="fa-solid fa-square-root-variable fa-lg me-2"></i></a>
        </div>
        <div class="col-lg-1 d-grid">
            <a class="btn btn-secondary btn-sm fw-bold" href="{{ route('banners') }}">Cancel</a>
        </div>
    </div>
    <hr>
</div>
<div class="container mt-3">
    @include('inc.standard-alerts')
    <div class="row">
        <div class="col-lg-6">
            <p class="fs-5 m-0 mb-3 fw-bold">Preview with Grid System</p>
            <img class="img-fluid" id="templateWithGrid" src="{{ asset($banner_template->file_path_drawed_grid_text.'/'.$banner_template->template->filename) }}" alt="{{ $banner_template->template->alias }}">
        </div>
        <div class="col-lg-6">
            <p class="fs-5 m-0 mb-3 fw-bold">Preview</p>
            <img class="img-fluid" id="renderedTemplate" src="{{ asset($banner_template->file_path_drawed_text.'/'.$banner_template->template->filename) }}" alt="{{ $banner_template->template->alias }}">
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-lg-12">
            <p class="fs-5 m-0 mb-2 fw-bold">About the Grid System</p>
            <p>With the help of the grid system, you are able to faster identify, which X-Y-Coordinates you need to put your text at the correct position.<br>
                Each horizontal and vertical line of the grid system represents 25px. In the left top corner, X and Y is 0px.
            </p>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <p class="m-0 fw-bold">Get X-Y-Coordinates</p>
            <p class="m-0 text-muted">Click on the position in the Grid System Image to get the X-Y-Coordinates</p>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-lg-3">
            <label for="x_coordinate_preview" class="form-label">X-Coordinate (horizontal)</label>
            <input type="number" class="form-control" name="x_coordinate_preview" id="x_coordinate_preview" min="0" max="{{ $banner_template->template->width }}" value="0" readonly>
        </div>
        <div class="col-lg-3">
            <label for="y_coordinate_preview" class="form-label">Y-Coordinate (vertical)</label>
            <input type="number" class="form-control" name="y_coordinate_preview" id="y_coordinate_preview" min="0" max="{{ $banner_template->template->height }}" value="0" readonly>
        </div>
    </div>
    <hr>
    <div class="row">
        <form id="template-configuration" method="POST" action="{{ route('banner.template.configuration.upsert', ['banner_template_id' => $banner_template->id]) }}" class="row g-3 needs-validation" novalidate>
            @method('patch')
            @csrf
            <div class="col-lg-12">
                <div class="accordion" id="accordionPanelsStayOpen">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="redirectUrlHeading">
                            <a class="accordion-button text-decoration-none text-dark fw-bold bg-light" data-bs-toggle="collapse" data-bs-target="#redirectUrl" aria-expanded="true" aria-controls="redirectUrl">
                                <div class="col-lg-9">
                                    Redirect URL
                                </div>
                                <div class="col-lg-2">
                                    @if(isset($banner_template) && $banner_template->redirect_url != null)
                                        <span class="badge text-bg-success ms-2">Active</span>
                                    @else
                                        <span class="badge text-bg-warning ms-2">Not configured</span>
                                    @endif
                                </div>
                            </a>
                        </h2>
                        <div id="redirectUrl" class="accordion-collapse collapse show" aria-labelledby="redirectUrl">
                            <div class="accordion-body">
                                <div class="input-group">
                                    <input class="form-control" type="url" id="validationRedirectUrl" name="redirect_url" value="{{ old('redirect_url', (isset($banner_template)) ? $banner_template->redirect_url : '') }}" placeholder="e.g. https://example.com/news" aria-label="validationRedirectUrl">
                                    <button class="btn btn-outline-primary" type="button">Test Redirect</button>
                                </div>
                                <div class="valid-feedback">{{ __("Looks good!") }}</div>
                                <div class="invalid-feedback">{{ __("Please provide a valid URL.") }}</div>
                                <div class="form-text">
                                    An optional URL, where the user should get redirected, when he clicks on the banner. By default, the rendered template will be opened.
                                </div>
                                <p class="mt-2">For this functionality you need to configure the following URL as Hostbanner URL on your TeamSpeak server:<br>
                                    <code>https://banner.highend-gaming.com/api/v1/banner/1jdxvi25/redirect-url</code>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="autoDisableHeading">
                            <a class="accordion-button text-decoration-none text-dark fw-bold bg-light" data-bs-toggle="collapse" data-bs-target="#autoDisable" aria-expanded="false" aria-controls="autoDisable">
                                <div class="col-lg-9">
                                    Automatic Disabling
                                </div>
                                <div class="col-lg-2">
                                    @if(isset($banner_template) && $banner_template->disable_at != null)
                                        <span class="badge text-bg-success ms-2">Active</span>
                                    @else
                                        <span class="badge text-bg-warning ms-2">Not configured</span>
                                    @endif
                                </div>
                            </a>
                        </h2>
                        <div id="autoDisable" class="accordion-collapse collapse show" aria-labelledby="autoDisable">
                            <div class="accordion-body">
                                <input class="form-control" type="datetime-local" id="validationDisableAt" name="disable_at" value="{{ old('disable_at', (isset($banner_template)) ? $banner_template->disable_at : '') }}" aria-label="validationDisableAt">
                                <div class="valid-feedback">{{ __("Looks good!") }}</div>
                                <div class="invalid-feedback">{{ __("Please provide a valid datetime.") }}</div>
                                <div class="form-text">
                                    Define an optional date and time, when this configuration should be automatically disabled. Leave it unset to not automatically disable it.
                                </div>
                                <p class="mt-2">This function is for example useful when you announce an event on your banner for a specific date (and time).
                                    When you set here the respective date (and time) the dynamic banner will automatically disable this configured template for you afterward,
                                    so that you don't have to disable it manually.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="bannerConfigHeading">
                            <a class="accordion-button text-decoration-none text-dark fw-bold bg-light" data-bs-toggle="collapse" data-bs-target="#bannerConfig" aria-expanded="false" aria-controls="bannerConfig">
                                <div class="col-lg-9">
                                    Banner Configurations
                                </div>
                                <div class="col-lg-2">
                                    @if($banner_template->configurations->count() > 0)
                                        <span class="badge text-bg-success ms-2">{{$banner_template->configurations->count()}} active configurations</span>
                                    @else
                                        <span class="badge text-bg-warning ms-2">No configurations</span>
                                    @endif
                                </div>
                            </a>
                        </h2>
                        <div id="bannerConfig" class="accordion-collapse collapse show" aria-labelledby="autoDisable">
                            <div class="accordion-body">
                                @if($banner_template->configurations->count() > 0)
                                    @foreach($banner_template->configurations as $configuration)
                                    <div class="mt-2" id="config-row-{{ $configuration->id }}">
                                        @include('inc.banners.banner-config-input', ['template' => $banner_template->template, 'configuration' => $configuration])
                                    </div>
                                    @endforeach
                                    @php unset($configuration) @endphp
                                    <div id="new-config-row-1" class="d-none">
                                        @include('inc.banners.banner-config-input', ['template' => $banner_template->template])
                                    </div>
                                @else
                                    <div id="new-config-row-1">
                                        @include('inc.banners.banner-config-input', ['template' => $banner_template->template])
                                    </div>
                                @endif
                                <div class="row mb-2 justify-content-between">
                                    <div class="col-lg-2">
                                        <button type="button" class="form-control btn btn-success" id="add-config-row">Add Row</button>
                                    </div>
                                    <div class="col-lg-2">
                                        <button type="submit" class="form-control btn btn-primary" id="add-config-row">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <hr>
</div>

@include('inc.bs-tooltip')
@include('inc.banner-config-script')
@include('inc.form-validation')
@foreach($instance as $instanceVariableModal)
    @include('modals.modal-variables', ['instanceVariableModal'=>$instanceVariableModal])
@endforeach
@endsection
