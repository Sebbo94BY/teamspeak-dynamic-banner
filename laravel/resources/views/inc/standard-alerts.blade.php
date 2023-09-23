@if (session('success'))
    <div class="row mt-2">
        <div class="col-lg-12">
            <div class="alert alert-success" role="alert">
                {{ session('message') }}
            </div>
        </div>
    </div>
@endif
@if (session('error'))
    <div class="row mt-2">
        <div class="col-lg-12">
            <div class="alert alert-danger" role="alert">
                {{ session('message') }}
            </div>
        </div>
    </div>
@endif
@if ($errors->any())
    <div class="row mt-2">
        <div class="col-lg-12">
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif
