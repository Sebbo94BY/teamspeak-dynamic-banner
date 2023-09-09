<div class="modal fade" id="delBanner-{{$bannerDeleteModal->id}}" tabindex="-1" aria-labelledby="delBanner-{{$bannerDeleteModal->id}}-Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="delBanner-{{$bannerDeleteModal->id}}-Label">Delete Banner</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="{{ route('banner.delete', ['banner_id' => $bannerDeleteModal->id]) }}" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>