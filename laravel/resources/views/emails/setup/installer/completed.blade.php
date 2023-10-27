<x-mail::message>
{!! __('views/emails/setup/installer/completed.greet_user', ['username' => $user->name]) !!},

{!! __('views/emails/setup/installer/completed.installation_completed') !!}

{{ __('views/emails/setup/installer/completed.check_system_status') }}:

<x-mail::button :url="route('administration.systemstatus')">
{!! __('views/emails/setup/installer/completed.open_system_status_page_button') !!}
</x-mail::button>

{{ __('views/emails/setup/installer/completed.next_steps') }}:

<ol>
    <li>{!! __('views/emails/setup/installer/completed.next_steps_download_install_ttf_fonts') !!}</li>
    <li>{!! __('views/emails/setup/installer/completed.next_steps_add_configure_instance') !!}</li>
    <li>{{ __('views/emails/setup/installer/completed.next_steps_start_instance') }}</li>
    <li>{!! __('views/emails/setup/installer/completed.next_steps_upload_templates') !!}</li>
    <li>{!! __('views/emails/setup/installer/completed.next_steps_add_banner') !!}</li>
    <li>{{ __('views/emails/setup/installer/completed.next_steps_add_templates_to_banner') }}</li>
    <li>{{ __('views/emails/setup/installer/completed.next_steps_configure_banner_templates') }}</li>
    <li>{{ __('views/emails/setup/installer/completed.next_steps_enable_banner_templates') }}</li>
    <li>{{ __('views/emails/setup/installer/completed.next_steps_configure_banner_api_url') }}</li>
</ol>

{!! __('views/emails/setup/installer/completed.github_feature_request_issue_hint') !!}

{{ __('views/emails/setup/installer/completed.automated_email_hint') }}


{{ __('views/emails/setup/installer/completed.closing_words') }},<br>
{{ config('app.name') }}
</x-mail::message>
