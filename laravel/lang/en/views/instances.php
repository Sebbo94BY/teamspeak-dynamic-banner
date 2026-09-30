<?php

return [

    /**
     * Instances Headline
     */
    'instances' => 'Instances',

    /**
     * Add Instance
     */
    'add_instance' => 'Add Instance',

    /**
     * Information Box
     */
    'no_instance_added_yet' => 'You haven\'t added any instance yet.',

    /**
     * Datatable
     */
    'table_status' => 'Status',
    'table_server_name' => 'Server Name',
    'table_host' => 'Host',
    'table_voice_port' => 'Voice Port',
    'table_client_nickname' => 'Client Nickname',
    'table_health' => 'Health check',
    'table_actions' => 'Actions',

    'table_status_stopped' => 'Stopped',
    'table_status_stopped_title' => 'The bot is not running and thus not collecting any current data.',
    'table_status_running' => 'Running',
    'table_status_running_title' => 'The bot is running as PID <b>:process_id</b> since <b>:started_at (:timezone)</b> and collecting current data.',
    'attention_stopped_filter' => 'Showing only stopped instances that need attention.',
    'starting_instance' => 'Starting instance. This may take a moment…',
    'stopping_instance' => 'Stopping instance. This may take a moment…',
    'restarting_instance' => 'Restarting instance. This may take a moment…',
    'status_refresh_scheduled' => 'The status refreshes automatically in :seconds seconds.',
    'status_refresh_progress' => 'Waiting for instance status refresh',
    'health_ok' => 'Healthy',
    'health_failed' => 'Problem detected',
    'health_pending' => 'Not checked yet',
    'health_ok_title' => 'The connection and every ServerQuery operation required by the application succeeded.',
    'health_failed_title' => 'At least one ServerQuery operation required by the application failed.',
    'health_last_error' => 'Latest runtime error',
    'health_last_error_resolved' => 'Latest runtime error (subsequently checked successfully)',
    'health_open_details' => 'Open health check details',
    'health_modal_title' => 'Health check: :virtualserver_name',
    'health_modal_close' => 'Close',
    'health_required_permissions' => 'Required ServerQuery permissions',
    'health_pending_title' => 'No background health check has run for this instance yet.',
    'health_last_checked_at' => 'Last health check: :time',
    'health_problem_since' => 'Problem exists since: :time',
    'bot_restart_scheduled' => 'Bot restart scheduled',
    'bot_restart_scheduled_at' => 'Restart scheduled for: :time',
    'bot_restart_reason' => 'Reason',
    'health_check_connection' => 'ServerQuery connection and login',
    'health_check_channels' => 'Read channels',
    'health_check_clients' => 'Read clients',
    'health_check_servergroups' => 'Read server groups',
    'health_check_servergroup_members' => 'Read server group members',
    'health_check_serverinfo' => 'Read virtual server',
    'health_check_connectioninfo' => 'Read connection statistics',
    'health_check_events' => 'Subscribe to server events',

];
