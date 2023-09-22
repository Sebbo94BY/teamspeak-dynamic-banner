<div class="modal fade" id="editBanner-{{$bannerEditModal->id}}" tabindex="-1" aria-labelledby="editBanner-{{$bannerEditModal->id}}-Label" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-bold" id="editBanner-{{$bannerEditModal->id}}-Label">Add Banner</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('banner.update', ['banner_id' => $bannerEditModal->id]) }}" class="row g-3 needs-validation" novalidate>
                @method('patch')
                @csrf
                <div class="modal-body">
                    <div class="col-lg-12">
                        <div class="mb-3 row">
                            <label for="validationName" class="col-lg-1 col-form-label fw-bold">Name</label>
                            <div class="col-lg-11">
                                <input type="text" class="form-control" id="validationName"
                                       name="name" value="{{ old('name', $bannerEditModal->name) }}"
                                       aria-describedby="validationNameHelp validationNameFeedback-{{$bannerEditModal->id}}"
                                       placeholder="e.g. My Banner or Games Banner" required>
                                <div id="validationNameHelp" class="form-text">A name for the banner configuration as identifier for you.</div>
                            </div>
                            <div class="valid-feedback">{{ __("Looks good!") }}</div>
                            <div id="validationNameFeedback-{{$bannerEditModal->id}}" class="invalid-feedback">{{ __("Please provide a valid alias.") }}</div>
                        </div>
                        <div class="mb-3 row">
                            <label for="validationInstanceId" class="col-lg-1 col-form-label fw-bold">Instance</label>
                            <div class="col-lg-11 col-form-label">
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
                                <div id="validationInstanceIdHelp" class="form-text">Please select your instance as data source for the banner.</div>
                                <div class="valid-feedback">{{ __("Looks good!") }}</div>
                                <div id="validationInstanceIdFeedback-{{$bannerEditModal->id}}" class="invalid-feedback">{{ __("Please provide a valid instance (ID).") }}</div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-1">
                                <label class="col-form-label fw-bold" for="validationRandomRotationEnabled">Enable</label>
                            </div>
                            <div class="col-lg-11 my-auto">
                                <input class="form-check-input" type="checkbox"
                                       id="validationRandomRotation" @if(old('random_rotation', $banner->random_rotation)) checked @endif
                                       name="random_rotation"
                                       aria-describedby="validationRandomRotationHelp validationRandomRotationFeedback-{{$bannerEditModal->id}}">
                                <label class="form-check-label" for="validationRandomRotation">Random template rotation</label>
                                <div id="validationRandomRotationHelp" class="form-text">
                                    When enabled, every client will see a different random template. If disabled, the same template will be shown to all clients.
                                </div>
                                <div class="valid-feedback">{{ __("Looks good!") }}</div>
                                <div id="validationRandomRotationFeedback-{{$bannerEditModal->id}}" class="invalid-feedback">{{ __("You can only enable or disable this checkbox.") }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
            @include('inc.form-validation')
        </div>
    </div>
</div>
