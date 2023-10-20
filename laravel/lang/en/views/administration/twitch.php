<?php

return [

    /**
     * Twitch Headline
     */
    'twitch' => 'Twitch',

    /**
     * Accordion
     */
    'api_credentials_accordion_headline' => 'Twitch API Credentials',

    'api_credentials_accordion_unconfigured' => 'Unconfigured',
    'api_credentials_accordion_invalid_credentials' => 'Invalid Credentials',
    'api_credentials_accordion_valid_credentials' => 'Valid Credentials',

    'api_usage_information' => 'To be able to use this integration, you need to register this application as an application at Twitch, so that you get API credentials.',
    'twitch_register_app_documentation' => 'Here you can find the detailed official documentation: <a href="https://dev.twitch.tv/docs/authentication/register-app">https://dev.twitch.tv/docs/authentication/register-app</a>',
    'installation_instructions' => 'Quick instructions',
    'step_login_or_create_twitch_account' => 'Login to your Twitch account or sign up for a new one, if you don\'t have any yet',
    'step_open_twitch_dev_console' => 'Open <a href="https://dev.twitch.tv/console">https://dev.twitch.tv/console</a>',
    'step_open_applications' => 'Click on <b>Applications</b>',
    'step_open_register_app' => 'Click on <b>Register your app</b>',
    'step_fill_out_the_form' => 'Fill out the form',
    'step_fill_out_the_form_name' => '<b>Name</b>: <code>teamspeak-dynamic-banner</code> (just an example; use whatever name, which you want - it\'s only visible on this Twitch page)',
    'step_fill_out_the_form_oauth_redirect_urls' => '<b>OAuth Redirect URLs</b>: <code>http://localhost</code> (not relevant, so simply set this for example)',
    'step_fill_out_the_form_category' => '<b>Category</b>: <code>Website Integration</code>',
    'step_open_new_app' => 'Click on <b>Manage</b> next to your new application',
    'step_copy_and_insert_client_id' => 'Afterwards, you will see the <code>Client-ID</code> of your application. Insert this value into the <b>Client ID</b> field here.',
    'step_copy_and_insert_client_secret' => 'Click on <b>New secret</b>. You will temporary see/get the <code>Client-Secret</code> of your application. Insert this value into the <b>Client Secret</b> field here.',
    'step_submit_form' => 'Submit the form',

    'api_client_id' => 'Client ID',
    'api_client_id_placeholder' => 'e. g. 1ec8e09a145fc972b5eed9d1deb51631',
    'api_client_id_help' => 'The client ID of your Twitch API credentials.',

    'api_client_secret' => 'Client Secret',
    'api_client_secret_placeholder' => 'e. g. ca733c04c302365cc782283ed5b7d39a',
    'api_client_secret_help' => 'The client secret of your Twitch API credentials.',

    /**
     * Buttons
     */
    'save_button' => 'Save',
    'delete_api_credentials_button' => 'Delete API credentials',
    'add_streamer_button' => 'Add Twitch streamer',

    /**
     * Information Box
     */
    'no_permissions_to_edit_the_api_credentials' => 'You do not have permissions to view and change the API credentials. Please contact an admin.',
    'no_streamer_added_info' => 'No Twitch streamer have been added yet.',
    'no_twitch_api_credentials_are_configured' => 'This integration will only work, when you configure valid Twitch API credentials. This integration will currently neither pull any Twitch stream information, nor enable or disable your banner templates when configured.',

    /**
     * Datatable
     */
    'table_stream_status' => 'Status',
    'table_stream_url' => 'Stream URL',
    'table_last_modified' => 'Last Modified',

    'table_stream_status_online' => 'Online',
    'table_stream_status_offline' => 'Offline',

    /**
     * Form Validation
     */
    'form_validation_looks_good' => 'Looks good!',
    'api_client_id_validation_error' => 'Please provide a valid client ID!',
    'api_client_secret_validation_error' => 'Please provide a valid client secret!',

];
