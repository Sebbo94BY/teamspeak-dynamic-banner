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
    'accordion_section_queue_health' => 'Hintergrundaufgaben',
    'accordion_section_queue_health_intro' => 'Hier siehst du, ob Aufgaben im Hintergrund zeitnah abgearbeitet werden und wie viele Worker sich für jede Queue melden.',
    'accordion_section_queue_health_default_queue' => 'Allgemeine Hintergrundaufgaben',
    'accordion_section_queue_health_matomo_queue' => 'Matomo-Tracking',
    'accordion_section_queue_health_workers' => 'Aktive Worker',
    'accordion_section_queue_health_workers_current_value' => '{0}Keine aktiven Worker|{1}:count aktiver Worker|[2,*]:count aktive Worker',
    'accordion_section_queue_health_workers_required_value' => 'Worker melden sich regelmäßig und verarbeiten diese Queue.',
    'accordion_section_queue_health_workers_missing_action' => 'Kein Worker wurde in den letzten 90 Sekunden erkannt. Prüfe Supervisor mit `supervisorctl status` und starte die Worker bei Bedarf neu.',
    'accordion_section_queue_health_workers_unavailable' => 'Nicht verfügbar',
    'accordion_section_queue_health_workers_unavailable_action' => 'Für die Worker-Erkennung wird eine funktionierende Redis-Verbindung benötigt.',
    'accordion_section_queue_health_throughput' => 'Abgeschlossene Aufgaben',
    'accordion_section_queue_health_throughput_current_value' => '{0}Keine Aufgabe in den letzten 5 Minuten|{1}:count Aufgabe in den letzten 5 Minuten|[2,*]:count Aufgaben in den letzten 5 Minuten',
    'accordion_section_queue_health_throughput_required_value' => 'Zeigt den tatsächlichen Durchsatz, auch wenn Aufgaben zwischen zwei Momentaufnahmen fertig werden.',
    'accordion_section_queue_health_recommendation' => 'Empfehlung',
    'accordion_section_queue_health_recommendation_required_value' => 'Die Empfehlung basiert auf dem Rückstau, aktiven Workern und dem Durchsatz der letzten 5 Minuten.',
    'accordion_section_queue_health_recommendation_no_action' => 'Keine Änderung erforderlich',
    'accordion_section_queue_health_recommendation_start_worker' => 'Starte mindestens einen Worker für diese Queue',
    'accordion_section_queue_health_recommendation_observe' => 'Durchsatz weiter beobachten; noch keine belastbare Worker-Empfehlung möglich',
    'accordion_section_queue_health_recommendation_add_workers' => '{1}Empfohlen: 1 weiterer Worker|[2,*]Empfohlen: :count weitere Worker',
    'accordion_section_queue_health_history_label' => 'Verlauf: :range',
    'accordion_section_queue_health_history_collecting' => 'Verlauf wird aufgebaut …',
    'accordion_section_queue_health_history_range' => 'Zeitraum',
    'accordion_section_queue_health_history_range_apply' => 'Anzeigen',
    'accordion_section_queue_health_history_range_30m' => '30 Minuten',
    'accordion_section_queue_health_history_range_6h' => '6 Stunden',
    'accordion_section_queue_health_history_range_1d' => '1 Tag',
    'accordion_section_queue_health_history_range_7d' => '7 Tage',
    'accordion_section_queue_health_history_range_30d' => '30 Tage',
    'accordion_section_queue_health_size' => 'Aufgaben in der Queue',
    'accordion_section_queue_health_size_required_value' => 'Summe aller unten aufgeschlüsselten Aufgaben. Die Anzahl allein sagt nicht aus, wie viele Worker benötigt werden.',
    'accordion_section_queue_health_ready_jobs' => 'Sofort ausführbare Aufgaben',
    'accordion_section_queue_health_ready_jobs_required_value' => 'Diese Aufgaben warten darauf, von einem Worker gestartet zu werden.',
    'accordion_section_queue_health_processing_jobs' => 'Wird gerade verarbeitet',
    'accordion_section_queue_health_processing_jobs_required_value' => 'Diese Aufgaben wurden von einem Worker übernommen und sollten nach Abschluss verschwinden.',
    'accordion_section_queue_health_scheduled_jobs' => 'Für später geplant',
    'accordion_section_queue_health_scheduled_jobs_current_value' => ':count (nächste in :wait)',
    'accordion_section_queue_health_scheduled_jobs_required_value' => 'Diese Aufgaben werden erst zu ihrem geplanten Zeitpunkt ausgeführt. In dieser Anwendung betrifft das normalerweise das Löschen temporärer Banner-Dateien nach einer Minute.',
    'accordion_section_queue_health_scheduled_jobs_delayed_action' => 'Die nächste Aufgabe ist ungewöhnlich weit in der Zukunft geplant. Prüfe die Serverzeit und die Anwendung, die die Aufgabe angelegt hat.',
    'accordion_section_queue_health_stale_jobs' => 'Erneut auszuführende Aufgaben',
    'accordion_section_queue_health_stale_jobs_required_value' => 'Ein Worker hat diese Aufgaben übernommen, aber nicht rechtzeitig abgeschlossen. Prüfe die Worker mit `supervisorctl status teamspeak-dynamic-banner-worker:*`.',
    'accordion_section_queue_health_oldest_job' => 'Wartezeit der ältesten sofort ausführbaren Aufgabe',
    'accordion_section_queue_health_oldest_job_empty' => 'Keine sofort ausführbare Aufgabe wartet auf einen Worker',
    'accordion_section_queue_health_oldest_job_unavailable' => 'Für diese Queue-Art nicht verfügbar',
    'accordion_section_queue_health_oldest_job_unavailable_action' => 'Die Wartezeit kann nur bei einer datenbankbasierten Queue direkt geprüft werden.',
    'accordion_section_queue_health_oldest_job_required_value' => 'Aufgaben sollten normalerweise innerhalb von :minutes Minuten starten.',
    'accordion_section_queue_health_oldest_job_delayed_action' => 'Bitte prüfe die Worker mit `supervisorctl status teamspeak-dynamic-banner-worker:*`. Sind sie aktiv, erhöhe die Anzahl schrittweise und beobachte die Wartezeit.',
    'accordion_section_queue_health_age_seconds' => '{1}:count Sekunde|[2,*]:count Sekunden',
    'accordion_section_queue_health_age_minutes' => '{1}:count Minute|[2,*]:count Minuten',
    'accordion_section_queue_health_age_hours' => '{1}:count Stunde|[2,*]:count Stunden',

    /**
     * Accordion Section "Redis"
     */
    'accordion_section_redis' => 'Redis',
    'accordion_section_redis_connection' => 'Redis Verbindung',
    'accordion_section_redis_connection_current_value_connected' => 'Verbunden',
    'accordion_section_redis_connection_current_value_error' => 'Fehler: :exception',
    'accordion_section_redis_connection_required_value' => '`.env` sollte gültige `REDIS_` Einstellungen haben',
    'accordion_section_redis_client' => 'Redis Client',
    'accordion_section_redis_client_required_value' => '`phpredis` wird für bessere Performance empfohlen; `predis` wird weiterhin unterstützt',

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
