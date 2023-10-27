<x-mail::message>
{!! __('views/emails/users/created.greet_user', ['username' => $user->name]) !!},

{!! __('views/emails/users/created.invited_by', ['app_name' => config('app.name'), 'username' => $auth_user->name]) !!}

{!! __('views/emails/users/created.initial_password', ['initial_password' => $initial_password]) !!}

<x-mail::button :url="route('login')">
{{ __('views/emails/users/created.open_login_page_button') }}
</x-mail::button>

{{ __('views/emails/users/created.automated_email_hint') }}


{{ __('views/emails/users/created.closing_words') }},<br>
{{ config('app.name') }}
</x-mail::message>
