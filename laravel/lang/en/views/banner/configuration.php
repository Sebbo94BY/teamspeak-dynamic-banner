<?php

return [

    /**
     * Banner Configuration Headline
     */
    'banner_configuration' => 'Banner Configuration',

    /**
     * Banner Configuration Buttons
     */
    'back_button' => 'Back',
    'test_redirect_button' => 'Test Redirect',
    'add_row_button' => 'Add Row',
    'save_button' => 'Save',

    /**
     * Previews
     */
    'preview_with_grid_system' => 'Preview with Grid System',
    'preview_without_grid_system' => 'Preview',

    /**
     * About Grid System
     */
    'about_grid_system' => 'About the Grid System',
    'grid_system_purpose' => 'With the help of the grid system, you are able to faster identify, which X-Y-Coordinates you need to put your text at the correct position.',
    'get_x_y_coordinates_on_click' => 'By clicking on a position on one of the previews, you will get the corresponding X and Y coordinates.',
    'grid_system_explanation' => 'Each horizontal and vertical line of the grid system represents 25px. In the left top corner, X and Y is 0px.',
    'x_coordinate' => 'X-Coordinate (horizontal)',
    'y_coordinate' => 'Y-Coordinate (vertical)',

    /**
     * Accordion Status
     */
    'accordion_status_not_configured' => 'Unconfigured',
    'accordion_status_configured' => 'Configured',
    'accordion_status_configured_but_ignored' => 'Ignored',
    'accordion_status_no_configurations' => 'No configurations',
    'accordion_status_has_configurations' => '{1} :count_configurations configuration|{2,*} :count_configurations configurations',

    /**
     * Accordions
     */
    'name_accordion_headline' => 'Name',
    'name_placeholder' => 'e. g. Event Announcement',
    'name_help' => 'What will this banner configuration be about? Give it a descriptive name.',

    'redirect_url_accordion_headline' => 'Redirect URL',
    'redirect_url_hostbanner_url' => 'For this functionality you need to configure the following URL as Hostbanner URL on your TeamSpeak server: <code>:hostbanner_url</code>',
    'redirect_url_placeholder' => 'e. g. https://example.com/news',
    'redirect_url_help' => 'An optional URL, where the user should get redirected, when he clicks on the banner. By default, the rendered template will be opened.',

    'disable_at_accordion_headline' => 'Automatic Disabling',
    'disable_at_use_case' => 'This function is for example useful when you announce an event on your banner for a specific date (and time). When you set here the respective date (and time) the dynamic banner will automatically disable this configured template for you afterward, so that you don\'t have to disable it manually.',
    'disable_at_help' => 'Define an optional date and time, when this configuration should be automatically disabled. Leave it unset to not automatically disable it.',

    'time_based_de_activation_accordion_headline' => 'Time-Based Acivation/Deactivation',
    'twitch_based_de_activation_no_twitch_api_credentials_are_configured' => 'This integration will only work, when you configure valid Twitch API credentials. This integration will currently neither pull any Twitch stream information, nor enable or disable your banner templates when configured.',
    'time_based_de_activation_use_case' => 'This function is for example useful when you want to show specific banner configurations only during a specific time window.',
    'time_based_de_activation_enable_at' => 'Enable at',
    'time_based_de_activation_enable_at_help' => 'Define an optional time, when this configuration should be automatically enabled. Leave it unset to not automatically enable it.',
    'time_based_de_activation_disable_at' => 'Disable at',
    'time_based_de_activation_disable_at_help' => 'Define an optional time, when this configuration should be automatically disabled. Leave it unset to not automatically disable it.',

    'twitch_based_de_activation_accordion_headline' => 'Twitch-Based Acivation/Deactivation',
    'twitch_based_de_activation_use_case' => 'This function is for example useful when you want to show specific banner configurations only when the respective Twitch streamer is online.',
    'twitch_based_de_activation_twitch_streamer_id' => 'Twitch Streamer',
    'twitch_based_de_activation_twitch_streamer_id_help' => 'Select an optional Twitch streamer to automatically enable and disable this banner template when the streamer is online or offline.',

    'text_configurations_accordion_headline' => 'Text Configurations',

    /**
     * Form Validation
     */
    'form_validation_looks_good' => 'Looks good!',
    'name_validation_error' => 'Please provide a valid file!',
    'redirect_url_validation_error' => 'Please provide a valid URL!',
    'disable_at_validation_error' => 'Please provide a valid datetime!',
    'time_based_de_activation_enable_at_validation_error' => 'Please provide a valid time!',
    'time_based_de_activation_disable_at_validation_error' => 'Please provide a valid time!',

];
