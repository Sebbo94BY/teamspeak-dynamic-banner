<div class="modal fade" id="addFont" tabindex="-1" aria-labelledby="addFontLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-bold" id="addFontLabel">Add new font</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('administration.font.create') }}" enctype="multipart/form-data" class="row g-3 needs-validation" novalidate>
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="alert alert-primary" role="alert">
                                <p>This project uses TrueType Fonts (TTF) for writing the text on the banner images.</p>
                                <p>Installation instructions:</p>
                                <ol>
                                    <li>Visit for example <a href="https://fontsource.org" target="_blank">Fontsource.org</a></li>
                                    <li>Specify filters or search for specific fonts</li>
                                    <li>Checkout the font previews to find a font, which you like and open the font details page</li>
                                    <li>Click on the download button for this specific font</li>
                                    <li>Unzip the recently downloaded ZIP file</li>
                                    <li>Select the required <code>*.ttf</code> file here</li>
                                    <li>Submit the form</li>
                                </ol>
                                <p>You can upload and use any TTF file - it does not have to be from Fontsource.</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label for="validationFile" class="form-label">Font</label>
                                <input class="form-control" id="validationFile" type="file" accept=".ttf" name="file" value="{{ old('file') }}" aria-describedby="fileHelp" required>
                                <div id="fileHelp" class="form-text">The fonts file. (TTF)</div>
                                <div class="valid-feedback">{{ __("Looks good!") }}</div>
                                <div class="invalid-feedback">{{ __("Please provide a valid file.") }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@include('inc.form-validation')