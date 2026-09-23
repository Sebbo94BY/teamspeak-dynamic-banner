<?php

return [

    /**
     * Installation Status Messages
     */
    'installation_has_no_errors' => 'There are no problems with your installation. You can pat yourself on the back.',
    'installation_has_warnings' => '{1} Your installation has :warning_count warning, which you might want to fix for the best software experience.|{2,*} Your installation has :warning_count warnings, which you might want to fix for the best software experience.',
    'installation_has_critical_errors' => '{1} Your installation has :danger_count critical issue, which you need to fix that everything works properly.|{2,*} Your installation has :danger_count critical issues, which you need to fix that everything works properly.',

    /**
     * Accordion Status
     */
    'accordion_error' => 'Error',
    'accordion_warning' => 'Warning',
    'accordion_operational' => 'Operational',

    /**
     * Icons Legend
     */
    'legend_label' => 'Legend',
    'icon_operational' => 'Operational (no issues)',
    'icon_warning' => 'Warning (limited functionality)',
    'icon_error' => 'Misconfiguration (something will not work)',
    'icon_information' => 'Information (just for your information)',

    /**
     * Accordion Section "PHP"
     */
    'accordion_section_php' => 'PHP',
    'accordion_section_php_version' => 'PHP Version',
    'accordion_section_php_extensions' => 'PHP Extensions',
    'accordion_section_php_ini_disable_functions_current_value_empty_list' => 'Empty list',
    'accordion_section_php_ini_disable_functions_required_value' => '`shell_exec` should not be listed',
    'accordion_section_php_ini_date_timezone_required_value' => 'should be set',

    /**
     * Accordion Section "Database"
     */
    'accordion_section_database' => 'Database',
    'accordion_section_database_connection' => 'Database Connection',
    'accordion_section_database_connection_current_value_connected' => 'Connected',
    'accordion_section_database_connection_current_value_error' => 'Error: :exception',
    'accordion_section_database_connection_required_value' => '`.env` should contain valid `DB_` settings',
    'accordion_section_database_name' => 'Database Name',
    'accordion_section_database_user' => 'Database User',
    'accordion_section_database_user_required_value' => 'should be a dedicated user and not `root`',
    'accordion_section_database_character_set' => 'Character Set',
    'accordion_section_database_character_set_required_value' => 'should be utf8-like',
    'accordion_section_database_collation' => 'Collation',
    'accordion_section_database_collation_required_value' => 'should be utf8-like',

    /**
     * Accordion Section "Permissions"
     */
    'accordion_section_permissions' => 'Permissions',
    'accordion_section_permissions_directories' => 'Directories',
    'accordion_section_permissions_directories_required_value' => 'must be writeable',

    /**
     * Accordion Section "Queue Health"
     */
    'accordion_section_queue_health' => 'Background tasks',
    'accordion_section_queue_health_intro' => 'This shows whether background tasks are processed promptly and how many workers are reporting for each queue.',
    'accordion_section_queue_health_default_queue' => 'General background tasks',
    'accordion_section_queue_health_matomo_queue' => 'Matomo tracking',
    'accordion_section_queue_health_workers' => 'Active workers',
    'accordion_section_queue_health_workers_current_value' => '{0}No active workers|{1}:count active worker|[2,*]:count active workers',
    'accordion_section_queue_health_workers_required_value' => 'Workers are reporting regularly and processing this queue.',
    'accordion_section_queue_health_workers_missing_action' => 'No worker was detected in the last 90 seconds. Check Supervisor with `supervisorctl status` and restart the workers if needed.',
    'accordion_section_queue_health_workers_unavailable' => 'Unavailable',
    'accordion_section_queue_health_workers_unavailable_action' => 'Worker detection requires a working Redis connection.',
    'accordion_section_queue_health_throughput' => 'Completed tasks',
    'accordion_section_queue_health_throughput_current_value' => '{0}No tasks in the last 5 minutes|{1}:count task in the last 5 minutes|[2,*]:count tasks in the last 5 minutes',
    'accordion_section_queue_health_throughput_required_value' => 'Shows actual throughput, even when tasks finish between two snapshots.',
    'accordion_section_queue_health_recommendation' => 'Recommendation',
    'accordion_section_queue_health_recommendation_required_value' => 'The recommendation uses the backlog, active workers, and throughput from the last 5 minutes.',
    'accordion_section_queue_health_recommendation_no_action' => 'No change required',
    'accordion_section_queue_health_recommendation_start_worker' => 'Start at least one worker for this queue',
    'accordion_section_queue_health_recommendation_observe' => 'Continue monitoring throughput; there is not enough data for a reliable worker recommendation yet',
    'accordion_section_queue_health_recommendation_add_workers' => '{1}Recommended: 1 additional worker|[2,*]Recommended: :count additional workers',
    'accordion_section_queue_health_history_label' => 'History: :range',
    'accordion_section_queue_health_history_collecting' => 'History is being collected …',
    'accordion_section_queue_health_history_range' => 'Period',
    'accordion_section_queue_health_history_range_apply' => 'Show',
    'accordion_section_queue_health_history_range_30m' => '30 minutes',
    'accordion_section_queue_health_history_range_6h' => '6 hours',
    'accordion_section_queue_health_history_range_1d' => '1 day',
    'accordion_section_queue_health_history_range_7d' => '7 days',
    'accordion_section_queue_health_history_range_30d' => '30 days',
    'accordion_section_queue_health_size' => 'Tasks in the queue',
    'accordion_section_queue_health_size_required_value' => 'Total of all task states listed below. The number alone does not indicate how many workers are needed.',
    'accordion_section_queue_health_ready_jobs' => 'Tasks ready to run',
    'accordion_section_queue_health_ready_jobs_required_value' => 'These tasks are waiting for a worker to start them.',
    'accordion_section_queue_health_processing_jobs' => 'Currently being processed',
    'accordion_section_queue_health_processing_jobs_required_value' => 'These tasks have been picked up by a worker and should disappear when complete.',
    'accordion_section_queue_health_scheduled_jobs' => 'Scheduled for later',
    'accordion_section_queue_health_scheduled_jobs_current_value' => ':count (next in :wait)',
    'accordion_section_queue_health_scheduled_jobs_required_value' => 'These tasks will run at their scheduled time. In this application, this normally removes temporary banner files after one minute.',
    'accordion_section_queue_health_scheduled_jobs_delayed_action' => 'The next task is scheduled unusually far in the future. Check the server time and the application that created the task.',
    'accordion_section_queue_health_stale_jobs' => 'Tasks to be retried',
    'accordion_section_queue_health_stale_jobs_required_value' => 'A worker picked up these tasks but did not finish them in time. Check the workers with `supervisorctl status teamspeak-dynamic-banner-worker:*`.',
    'accordion_section_queue_health_oldest_job' => 'Waiting time of the oldest task ready to run',
    'accordion_section_queue_health_oldest_job_empty' => 'No task ready to run is waiting for a worker',
    'accordion_section_queue_health_oldest_job_unavailable' => 'Not available for this queue driver',
    'accordion_section_queue_health_oldest_job_unavailable_action' => 'Waiting time can only be checked directly for a database-backed queue.',
    'accordion_section_queue_health_oldest_job_required_value' => 'Tasks should normally start within :minutes minutes.',
    'accordion_section_queue_health_oldest_job_delayed_action' => 'Check the workers with `supervisorctl status teamspeak-dynamic-banner-worker:*`. If they are active, increase their number gradually and observe the waiting time.',
    'accordion_section_queue_health_age_seconds' => '{1}:count second|[2,*]:count seconds',
    'accordion_section_queue_health_age_minutes' => '{1}:count minute|[2,*]:count minutes',
    'accordion_section_queue_health_age_hours' => '{1}:count hour|[2,*]:count hours',

    /**
     * Accordion Section "Redis"
     */
    'accordion_section_redis' => 'Redis',
    'accordion_section_redis_connection' => 'Redis Connection',
    'accordion_section_redis_connection_current_value_connected' => 'Connected',
    'accordion_section_redis_connection_current_value_error' => 'Error: :exception',
    'accordion_section_redis_connection_required_value' => '`.env` should contain valid `REDIS_` settings',
    'accordion_section_redis_client' => 'Redis Client',
    'accordion_section_redis_client_required_value' => '`phpredis` is recommended for better performance; `predis` remains supported',

    /**
     * Accordion Section "FFMpeg"
     */
    'accordion_section_ffmpeg' => 'FFMpeg (GIF Support)',
    'accordion_section_ffmpeg_version' => 'FFMpeg Version',
    'accordion_section_ffmpeg_version_current_value_error' => 'FFMpeg is either not installed or `shell_exec()` is disabled',
    'accordion_section_ffmpeg_version_required_value' => '`FFMpeg` should be installed to support GIF templates',

    /**
     * Accordion Section "Mail"
     */
    'accordion_section_mail' => 'Email (SMTP)',
    'accordion_section_mail_connection' => 'SMTP Connection',
    'accordion_section_mail_connection_current_value_connected' => 'Connected',
    'accordion_section_mail_connection_current_value_unsupported_mailer_for_testing' => 'Only SMTP based mailer can be currently tested.',
    'accordion_section_mail_connection_current_value_error' => 'Error: :exception',
    'accordion_section_mail_connection_required_value' => '`.env` should contain valid `MAIL_` settings',

    /**
     * Accordion Section "Version"
     */
    'accordion_section_version' => 'Version',
    'accordion_section_version_php' => 'PHP Version',
    'accordion_section_version_laravel' => 'Laravel Version',
    'accordion_section_version_bootstrap' => 'Bootstrap Version',
    'accordion_section_version_datatable' => 'Datatable Version',
    'accordion_section_version_jquery' => 'jQuery Version',

    /**
     * Accordion Section "Various"
     */
    'accordion_section_various' => 'Various',
    'accordion_section_various_git_deployment' => 'Is Git Deployment',
    'accordion_section_various_git_deployment_current_value_yes' => 'Yes',
    'accordion_section_various_git_deployment_current_value_no' => 'No',
    'accordion_section_various_git_commit_sha' => 'Installed Git Commit SHA',
    'accordion_section_various_git_commit_sha_unknown' => 'Unknown as it is no Git working directory',
    'accordion_section_various_app_env' => 'Application Environment',
    'accordion_section_various_app_env_required_value' => 'should be set to `production` in production',
    'accordion_section_various_app_debug' => 'Application Debug',
    'accordion_section_various_app_debug_current_value_enabled' => 'Enabled',
    'accordion_section_various_app_debug_current_value_disabled' => 'Disabled',
    'accordion_section_various_app_debug_required_value' => 'should be disabled in production',
    'accordion_section_various_server_software' => 'Server Software',
    'accordion_section_various_php_binary' => 'PHP Binary',

];
