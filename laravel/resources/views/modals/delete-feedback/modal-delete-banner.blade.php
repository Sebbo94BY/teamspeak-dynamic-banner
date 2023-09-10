<div class="modal fade" id="delBanner-{{$bannerDeleteModal->id}}" tabindex="-1" aria-labelledby="delBanner-{{$bannerDeleteModal->id}}-Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-bold" id="delBanner-{{$bannerDeleteModal->id}}-Label">Delete Banner</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="m-0">Are you really sure, that you want to delete <span class="fw-bold">{{$bannerDeleteModal->name}}</span> ?</p>
                <p class="m-0">This will also automatically delete all relations</p>
                <p class="fw-bold text-danger m-0">This action is not revertible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form method="post" action="{{ route('banner.delete', ['banner_id' => $bannerDeleteModal->id]) }}">
                    @method('delete')
                    @csrf
                    <button class="btn btn-danger" type="submit">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
