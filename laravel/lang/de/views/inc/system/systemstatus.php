<?php

return [

    /**
     * Installation Status Messages
     */
    'installation_has_no_errors' => 'Es gibt keine Probleme mit dieser Installation. Du kannst dir selbst auf die Schulter klopfen.',
    'installation_has_warnings' => '{1} Diese Installation weist :warning_count Warnung auf, die du vielleicht beheben möchtest, um die Software optimal nutzen zu können.|{2,*} Diese Installation weist :warning_count Warnungen auf, die du vielleicht beheben möchtest, um die Software optimal nutzen zu können.',
    'installation_has_critical_errors' => '{1} Diese Installation hat ein kritisches Problem, welches du beheben musst, damit alles richtig funktioniert.|{2,*} Diese Installation hat :danger_count kritische Probleme, welche du beheben musst, damit alles richtig funktioniert.',

    /**
     * Accordion Status
     */
    'accordion_error' => 'Fehler',
    'accordion_warning' => 'Warnung',
    'accordion_operational' => 'Operativ',

    /**
     * Icons Legend
     */
    'legend_label' => 'Legende',
    'icon_operational' => 'Operativ (keine Probleme)',
    'icon_warning' => 'Warnung (eingeschränkte Funktionalität)',
    'icon_error' => 'Fehlkonfiguration (etwas wird nicht funktionieren)',
    'icon_information' => 'Information (nur zu deiner Information)',

    /**
     * Accordion Section "PHP"
     */
    'accordion_section_php' => 'PHP',
    'accordion_section_php_version' => 'PHP Version',
    'accordion_section_php_extensions' => 'PHP Erweiterungen',
    'accordion_section_php_ini_disable_functions_current_value_empty_list' => 'Leere Liste',
    'accordion_section_php_ini_disable_functions_required_value' => '`shell_exec` sollte nicht gelistet sein',
    'accordion_section_php_ini_date_timezone_required_value' => 'sollte gesetzt sein',

    /**
     * Accordion Section "Database"
     */
    'accordion_section_database' => 'Datenbank',
    'accordion_section_database_connection' => 'Datenbank Verbindung',
    'accordion_section_database_connection_current_value_connected' => 'Verbunden',
    'accordion_section_database_connection_current_value_error' => 'Fehler: :exception',
    'accordion_section_database_connection_required_value' => '`.env` sollte gültige `DB_` Einstellungen haben',
    'accordion_section_database_name' => 'Datenbank Name',
    'accordion_section_database_user' => 'Datenbank Benutzer',
    'accordion_section_database_user_required_value' => 'sollte ein eigener Benutzer und nicht `root` sein',
    'accordion_section_database_character_set' => 'Character Set',
    'accordion_section_database_character_set_required_value' => 'sollte utf8-ähnlich sein',
    'accordion_section_database_collation' => 'Collation',
    'accordion_section_database_collation_required_value' => 'sollte utf8-ähnlich sein',

    /**
     * Accordion Section "Permissions"
     */
    'accordion_section_permissions' => 'Berechtigungen',
    'accordion_section_permissions_directories' => 'Verzeichnisse',
    'accordion_section_permissions_directories_required_value' => 'muss beschreibbar sein',

    /**
     * Accordion Section "Queue Health"
     */
    'accordion_section_queue_health' => 'Gesundheit der Queue',
    'accordion_section_queue_health_size' => 'Queue Größe',
    'accordion_section_queue_health_size_required_value' => 'Sollte immer möglichst wenig Jobs listen, da man sonst mehr Supervisor Worker benötigt. Sollte aktuell weniger als :max_expected_queue_size Jobs sein.',

    /**
     * Accordion Section "Redis"
     */
    'accordion_section_redis' => 'Redis',
    'accordion_section_redis_connection' => 'Redis Verbindung',
    'accordion_section_redis_connection_current_value_connected' => 'Verbunden',
    'accordion_section_redis_connection_current_value_error' => 'Fehler: :exception',
    'accordion_section_redis_connection_required_value' => '`.env` sollte gültige `REDIS_` Einstellungen haben',

    /**
     * Accordion Section "FFMpeg"
     */
    'accordion_section_ffmpeg' => 'FFMpeg (GIF Unterstützung)',
    'accordion_section_ffmpeg_version' => 'FFMpeg Version',
    'accordion_section_ffmpeg_version_current_value_error' => 'FFMpeg ist entweder nicht installiert oder `shell_exec()` ist deaktiviert',
    'accordion_section_ffmpeg_version_required_value' => '`FFMpeg` sollte installiert sein, um GIF Vorlagen zu unterstützen',

    /**
     * Accordion Section "Mail"
     */
    'accordion_section_mail' => 'E-Mail (SMTP)',
    'accordion_section_mail_connection' => 'SMTP Verbindung',
    'accordion_section_mail_connection_current_value_connected' => 'Verbunden',
    'accordion_section_mail_connection_current_value_unsupported_mailer_for_testing' => 'Nur SMTP basierte E-Mail Anbieter können aktuell getestet werden.',
    'accordion_section_mail_connection_current_value_error' => 'Fehler: :exception',
    'accordion_section_mail_connection_required_value' => '`.env` sollte gültige `MAIL_` Einstellungen haben',

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
    'accordion_section_various' => 'Verschiedenes',
    'accordion_section_various_git_deployment' => 'Ist ein Git Deployment',
    'accordion_section_various_git_deployment_current_value_yes' => 'Ja',
    'accordion_section_various_git_deployment_current_value_no' => 'Nein',
    'accordion_section_various_git_commit_sha' => 'Installierter Git Commit SHA',
    'accordion_section_various_git_commit_sha_unknown' => 'Unbekannt, da es kein Git Arbeitsverzeichnis ist',
    'accordion_section_various_app_env' => 'Application Environment',
    'accordion_section_various_app_env_required_value' => 'sollte in Produktion auf `production` gesetzt sein',
    'accordion_section_various_app_debug' => 'Application Debug',
    'accordion_section_various_app_debug_current_value_enabled' => 'Aktiviert',
    'accordion_section_various_app_debug_current_value_disabled' => 'Deaktiviert',
    'accordion_section_various_app_debug_required_value' => 'sollte in Produktiv deaktiviert sein',
    'accordion_section_various_server_software' => 'Server Software',
    'accordion_section_various_php_binary' => 'PHP Binary',

];
