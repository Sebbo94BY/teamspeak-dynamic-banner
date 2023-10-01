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
     * Accordion Section "Redis"
     */
    'accordion_section_redis' => 'Redis',
    'accordion_section_redis_connection' => 'Redis Connection',
    'accordion_section_redis_connection_current_value_connected' => 'Connected',
    'accordion_section_redis_connection_current_value_error' => 'Error: :exception',
    'accordion_section_redis_connection_required_value' => '`.env` should contain valid `REDIS_` settings',

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
    'accordion_section_various_app_env' => 'Application Environment',
    'accordion_section_various_app_env_required_value' => 'should be set to `production` in production',
    'accordion_section_various_app_debug' => 'Application Debug',
    'accordion_section_various_app_debug_current_value_enabled' => 'Enabled',
    'accordion_section_various_app_debug_current_value_disabled' => 'Disabled',
    'accordion_section_various_app_debug_required_value' => 'should be disabled in production',
    'accordion_section_various_server_software' => 'Server Software',
    'accordion_section_various_php_binary' => 'PHP Binary',

];
