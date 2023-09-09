<div class="modal fade" id="delFont-{{$fontDeleteModal->id}}" tabindex="-1" aria-labelledby="delFont-{{$fontDeleteModal->id}}-Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-bold" id="delFont-{{$fontDeleteModal->id}}-Label">Delete Font</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="m-0">Are you really sure, that you want to delete <span class="fw-bold">{{$fontDeleteModal->filename}}</span> ?</p>
                <p class="m-0">This will also automatically delete all relations</p>
                <p class="fw-bold text-danger m-0">This action is not revertible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="{{ route('administration.font.delete', ['font_id' => $fontDeleteModal->id]) }}" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>