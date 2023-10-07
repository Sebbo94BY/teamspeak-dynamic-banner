<div class="container mt-auto">
    <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top">
        <div class="col-md-12 d-flex align-items-center justify-content-between">
            <span class="text-muted">
                &copy; {{ Carbon\Carbon::now(Request::header('X-Timezone'))->format('Y')}} <a href="https://github.com/Sebbo94BY" target="_blank">Sebbo94BY</a>,
                <a href="https://github.com/Sebbo94BY/teamspeak-dynamic-banner/graphs/contributors" target="_blank">{{ __("views/layouts/footer-main.contributors") }}</a> |
                {{ __("views/layouts/footer-main.powered_by") }}
                <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true" title="HTML5"><i class="fa-brands fa-html5 text-primary fa-xl me-1"></i></span>
                <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true" title="PHP"><i class="fa-brands fa-php text-primary fa-xl me-1"></i></span>
                <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true" title="LARAVEL"><i class="fa-brands fa-laravel text-primary fa-xl me-1"></i></span>
                <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true" title="BOOTSTRAP"><i class="fa-brands fa-bootstrap text-primary fa-xl me-1"></i></span>
                <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true" title="FONT AWESOME"><i class="fa-solid fa-font-awesome text-primary fa-xl me-1"></i></span>
                <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true" title="GITHUB"><i class="fa-brands fa-github text-primary fa-xl me-1"></i></span>
            </span>
            <span>
                <i class="fa-solid fa-bullseye fa-fade fa-xl me-1 text-danger"></i>
                <a href="https://github.com/Sebbo94BY/teamspeak-dynamic-banner/issues/new/choose" target="_blank">{{ __("views/layouts/footer-main.feature_request_report_issue_link") }}</a>
            </span>
        </div>
    </footer>
</div>
