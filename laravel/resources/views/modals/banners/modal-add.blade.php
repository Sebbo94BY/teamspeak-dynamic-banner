<div class="modal fade" id="addBanner" tabindex="-1" aria-labelledby="addBannerLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-bold" id="addBannerLabel">{{ __('views/modals/banners/modal-add.add_banner') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('banner.save') }}" class="row g-3 needs-validation" novalidate>
                @csrf
                <div class="modal-body">
                    <div class="col-lg-12">
                        <div class="mb-3 row">
                            <label for="validationName" class="form-check-label fw-bold">{{ __('views/modals/banners/modal-add.name') }}</label>
                            <div class="col-lg-12">
                                <input type="text" class="form-control" id="validationName" name="name" value="{{ old('name') }}"
                                    aria-describedby="validationNameHelp validationNameFeedback" placeholder="{{ __('views/modals/banners/modal-add.name_placeholder') }}" required>
                                <div id="validationNameHelp" class="form-text">{{ __('views/modals/banners/modal-add.name_help') }}</div>
                            </div>
                            <div class="valid-feedback">{{ __('views/modals/banners/modal-add.form_validation_looks_good') }}</div>
                            <div id="validationNameFeedback" class="invalid-feedback">{{ __('views/modals/banners/modal-add.name_validation_error') }}</div>
                        </div>
                        <div class="mb-3 row">
                            <label for="validationInstanceId" class="form-check-label fw-bold">{{ __('views/modals/banners/modal-add.instance_id') }}</label>
                            <div class="col-lg-12 col-form-label">
                                <select class="form-select" id="validationInstanceId" name="instance_id" aria-describedby="validationInstanceIdHelp validationInstanceIdFeedback" required>
                                    @foreach ($instance_list as $instance)
                                        @if (old('instance_id', $instance->instance_id) == $instance->id) "selected"
                                        <option value="{{ $instance->id }}" selected>{{ $instance->virtualserver_name }} ({{ $instance->host }}:{{ $instance->voice_port }})</option>
                                        @else
                                            <option value="{{ $instance->id }}">{{ $instance->virtualserver_name }} ({{ $instance->host }}:{{ $instance->voice_port }})</option>
                                        @endif
                                    @endforeach
                                </select>
                                <div id="validationInstanceIdHelp" class="form-text">{{ __('views/modals/banners/modal-add.instance_id_help') }}</div>
                                <div class="valid-feedback">{{ __('views/modals/banners/modal-add.form_validation_looks_good') }}</div>
                                <div id="validationInstanceIdFeedback" class="invalid-feedback">{{ __('views/modals/banners/modal-add.instance_id_validation_error') }}</div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-12 my-auto">
                                <label class="form-check-label fw-bold" for="validationRandomRotation">{{ __('views/modals/banners/modal-add.random_rotation') }}</label>
                                <input class="form-check-input" type="checkbox"
                                       id="validationRandomRotation"
                                       name="random_rotation"
                                       aria-describedby="validationRandomRotationHelp validationRandomRotationFeedback" @if(old('random_rotation')) checked @endif>
                                <div id="validationRandomRotationHelp" class="form-text">{{ __('views/modals/banners/modal-add.random_rotation_help') }}</div>
                                <div class="valid-feedback">{{ __('views/modals/banners/modal-add.form_validation_looks_good') }}</div>
                                <div id="validationRandomRotationFeedback" class="invalid-feedback">{{ __('views/modals/banners/modal-add.random_rotation_validation_error') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('views/modals/banners/modal-add.dismiss_button') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('views/modals/banners/modal-add.add_button') }}</button>
                </div>
            </form>
            @include('inc.form-validation')
        </div>
    </div>
</div>
