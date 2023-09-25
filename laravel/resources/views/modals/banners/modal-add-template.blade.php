<div class="modal fade" id="addBannerTemplate" tabindex="-1" aria-labelledby="addBannerTemplateLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-bold" id="addBannerTemplateLabel">Add a template to your banner. </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col">Banner</th>
                        <th scope="col">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($templates as $template)
                        <form method="post" action="{{ route('banner.add.template') }}">
                            @csrf
                            <tr>
                                <td class="col-lg-6 text-start">
                                    <img class="img-fluid shadow-lg p-1 mb-2 bg-white rounded" src="{{ asset($template->file_path_original.'/'.$template->filename) }}" alt="{{ $template->alias }}">
                                </td>
                                <td class="col-lg-5">
                                    <label for="validationName" class="form-label fw-bold">Name</label>
                                    <input class="form-control" id="validationName" type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Event Announcement" aria-describedby="nameHelp" required>
                                    <div id="nameHelp" class="form-text">What will this template be about? Give it a descriptive name.</div>
                                    <div class="valid-feedback">{{ __("Looks good!") }}</div>
                                    <div class="invalid-feedback">{{ __("Please provide a valid name.") }}</div>
                                </td>
                                <td class="col-lg-1">
                                    <div class="d-flex justify-content-center">
                                        <input type="hidden" name="banner_id" value="{{ $banner->id }}">
                                        <input type="hidden" name="template_id" value="{{ $template->id }}">
                                        <button class="btn btn-success" type="submit"
                                                data-bs-toggle="tooltip" data-bs-html="true"
                                                title="Add this template. Make it configurable."
                                                id="add-badge">
                                            <i class="fa-solid fa-add fa-lg me-1"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </form>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @include('inc.form-validation')
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
