<div class="modal fade" id="delFont-{{$fontDeleteModal->id}}" tabindex="-1" aria-labelledby="delFont-{{$fontDeleteModal->id}}-Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="delFont-{{$fontDeleteModal->id}}-Label">Delete Font</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="{{ route('administration.font.delete', ['font_id' => $fontDeleteModal->id]) }}" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>