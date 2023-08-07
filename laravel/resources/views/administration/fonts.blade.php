@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Fonts') }}</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('message') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('message') }}
                        </div>
                    @endif

                    <div class="alert alert-primary" role="alert">
                        <p>This project uses <i>TrueType Fonts (TTF)</i> for writing the text on the banner images.</p>

                        <p>In general, you can use and upload any TTF file, but this project is optimized for the usage of <a href="https://fontsource.org" target="_blank">Fontsource.org</a>.</p>

                        <p>Installation instructions:</p>
                        <ol>
                            <li>Visit <a href="https://fontsource.org" target="_blank">Fontsource.org</a></li>
                            <li>Enable the checkbox for <code>Show only variable fonts</code> in the filter section</li>
                            <li>Specify further filters or search for specific fonts</li>
                            <li>Checkout the font previews to find a font, which you like and open the font details page</li>
                            <li>Click on the download button for this specific font</li>
                            <li>Unzip the recently downloaded ZIP file</li>
                            <li>Upload the required <code>*.ttf</code> file(s) to this project under <code>laravel/public/fonts/</code></li>
                            <li>Refresh this page to validate that your font has been properly installed</li>
                        </ol>
                    </div>

                    <table id="fonts" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>File</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fonts as $key => $fontfile)
                            <tr>
                                <td>{{ $key }}</td>
                                <td>{{ preg_replace("/fonts\//", '', $fontfile) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="module">
    $(document).ready(function () {
        $('#fonts').DataTable();
    });
</script>
@endsection
