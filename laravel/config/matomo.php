<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Matomo Enabled
    |--------------------------------------------------------------------------
    |
    | This value determines if this application should track HTTP requests at
    | all or not. If set to `true`, it will send the data to your specified
    | Matomo installation. Note, that you need to specify all below environment
    | variables in your `.env` file.
    |
    */

    'enabled' => (bool) env('MATOMO_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Matomo Base URL
    |--------------------------------------------------------------------------
    |
    | This value determines your Matomo base URL, where the API calls should
    | send the tracking data to. The URL usually looks like one of these:
    | - https://matomo.example.com/
    | - https://matomo.example.com/matomo/
    |
    */

    'base_url' => env('MATOMO_BASE_URL'),

    /*
    |--------------------------------------------------------------------------
    | Matomo Site ID
    |--------------------------------------------------------------------------
    |
    | This value determines the "website" in your Matomo installation, where
    | the tracked data should be saved.
    |
    */

    'site_id' => (int) env('MATOMO_SITE_ID'),

    /*
    |--------------------------------------------------------------------------
    | Matomo Heartbeat Timer
    |--------------------------------------------------------------------------
    |
    | This value determines how often Matomo should check, that the client is
    | still there. Decreasing this value increases the precission of the users
    | page view time. However, it also increases the web server load depending
    | on the website traffic, so you should keep an eye on the performance.
    | Increasing the value reduces the precission of the users page view time
    | and also the web server load.
    |
    */

    'heartbeat_timer' => (int) env('MATOMO_HEARTBEAT_TIMER', 30),

];
