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
                    <tr>
                        <td class="col-lg-10 text-start">
                            <img class="img-fluid w-50" src="{{ asset($template->file_path_original.'/'.$template->filename) }}" alt="{{ $template->alias }}">
                        </td>
                        <td class="col-lg-2">
                            <form method="post" action="{{ route('banner.add.template') }}">
                                @csrf
                                <input type="hidden" name="banner_id" value="{{ $banner->id }}">
                                <input type="hidden" name="template_id" value="{{ $template->id }}">
                                <button class="btn btn-success" type="submit"
                                        data-bs-toggle="tooltip" data-bs-html="true"
                                        title="Add this template. Make it configurable."
                                        id="add-badge">
                                    <i class="fa-solid fa-add fa-lg me-1"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @include('inc.bs-tooltip')
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>