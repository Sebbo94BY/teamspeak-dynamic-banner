<div class="modal fade" id="editBanner-{{$bannerEditModal->id}}" tabindex="-1" aria-labelledby="editBanner-{{$bannerEditModal->id}}-Label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-bold" id="editBanner-{{$bannerEditModal->id}}-Label">{{ __('views/modals/banners/modal-edit.edit_banner') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('banner.update', ['banner_id' => $bannerEditModal->id]) }}" class="row g-3 needs-validation" novalidate>
                @method('patch')
                @csrf
                <div class="modal-body">
                    <div class="col-lg-12">
                        <div class="mb-3 row">
                            <label for="validationName" class="form-check-label fw-bold">{{ __('views/modals/banners/modal-edit.name') }}</label>
                            <div class="col-lg-12">
                                <input type="text" class="form-control" id="validationName"
                                       name="name" value="{{ old('name', $bannerEditModal->name) }}"
                                       aria-describedby="validationNameHelp validationNameFeedback-{{$bannerEditModal->id}}"
                                       placeholder="{{ __('views/modals/banners/modal-edit.name_placeholder') }}" required>
                                <div id="validationNameHelp" class="form-text">{{ __('views/modals/banners/modal-edit.name_help') }}</div>
                            </div>
                            <div class="valid-feedback">{{ __('views/modals/banners/modal-edit.form_validation_looks_good') }}</div>
                            <div id="validationNameFeedback-{{$bannerEditModal->id}}" class="invalid-feedback">{{ __('views/modals/banners/modal-edit.name_validation_error') }}</div>
                        </div>
                        <div class="mb-3 row">
                            <label for="validationInstanceId" class="form-check-label fw-bold">{{ __('views/modals/banners/modal-edit.instance_id') }}</label>
                            <div class="col-lg-12 col-form-label">
                                <select class="form-select" id="validationInstanceId"
                                        name="instance_id"
                                        aria-describedby="validationInstanceIdHelp validationInstanceIdFeedback-{{$bannerEditModal->id}}" required>
                                    @foreach ($instance_list as $instance)
                                        @if (old('instance_id', $instance->instance_id) == $instance->id) "selected"
                                        <option value="{{ $instance->id }}" selected>{{ $instance->virtualserver_name }} ({{ $instance->host }}:{{ $instance->voice_port }})</option>
                                        @else
                                            <option value="{{ $instance->id }}">{{ $instance->virtualserver_name }} ({{ $instance->host }}:{{ $instance->voice_port }})</option>
                                        @endif
                                    @endforeach
                                </select>
                                <div id="validationInstanceIdHelp" class="form-text">{{ __('views/modals/banners/modal-edit.instance_id_help') }}</div>
                                <div class="valid-feedback">{{ __('views/modals/banners/modal-edit.form_validation_looks_good') }}</div>
                                <div id="validationInstanceIdFeedback-{{$bannerEditModal->id}}" class="invalid-feedback">{{ __('views/modals/banners/modal-edit.instance_id_validation_error') }}</div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-12 my-auto">
                                <label class="form-check-label fw-bold" for="validationRandomRotation">{{ __('views/modals/banners/modal-edit.random_rotation') }}</label>
                                <input class="form-check-input" type="checkbox"
                                       id="validationRandomRotation" @if (old('random_rotation', $banner->random_rotation)) checked @endif
                                       name="random_rotation"
                                       aria-describedby="validationRandomRotationHelp validationRandomRotationFeedback-{{$bannerEditModal->id}}">
                                <div id="validationRandomRotationHelp" class="form-text">{{ __('views/modals/banners/modal-edit.random_rotation_help') }}</div>
                                <div class="valid-feedback">{{ __('views/modals/banners/modal-edit.form_validation_looks_good') }}</div>
                                <div id="validationRandomRotationFeedback-{{$bannerEditModal->id}}" class="invalid-feedback">{{ __('views/modals/banners/modal-edit.random_rotation_validation_error') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('views/modals/banners/modal-edit.dismiss_button') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('views/modals/banners/modal-edit.update_button') }}</button>
                </div>
            </form>
            @include('inc.form-validation')
        </div>
    </div>
</div>
