<?php

return [

    /**
     * Instances Headline
     */
    'instances' => 'Instanzen',

    /**
     * Add Instance
     */
    'add_instance' => 'Instanz hinzufügen',

    /**
     * Information Box
     */
    'no_instance_added_yet' => 'Du hast noch keine Instanz hinzugefügt.',

    /**
     * Datatable
     */
    'table_status' => 'Status',
    'table_server_name' => 'Servername',
    'table_host' => 'Host',
    'table_voice_port' => 'Voice Port',
    'table_client_nickname' => 'Client Nickname',
    'table_health' => 'Health-Check',
    'table_actions' => 'Aktionen',

    'table_status_stopped' => 'Gestoppt',
    'table_status_stopped_title' => 'Der Bot ist nicht in Betrieb und sammelt daher keine aktuellen Daten.',
    'table_status_running' => 'In Betrieb',
    'table_status_running_title' => 'Der Bot läuft seit <b>:started_at (:timezone)</b> unter der PID <b>:process_id</b> und sammelt aktuelle Daten.',
    'attention_stopped_filter' => 'Es werden nur gestoppte Instanzen angezeigt, die Aufmerksamkeit benötigen.',
    'starting_instance' => 'Instanz wird gestartet. Dies kann einen Moment dauern…',
    'stopping_instance' => 'Instanz wird gestoppt. Dies kann einen Moment dauern…',
    'restarting_instance' => 'Instanz wird neu gestartet. Dies kann einen Moment dauern…',
    'status_refresh_scheduled' => 'Der Status wird in :seconds Sekunden automatisch aktualisiert.',
    'status_refresh_progress' => 'Warten auf die Statusaktualisierung der Instanz',
    'health_ok' => 'Fehlerfrei',
    'health_failed' => 'Problem erkannt',
    'health_pending' => 'Noch nicht geprüft',
    'health_ok_title' => 'Verbindung und alle von der Anwendung benötigten ServerQuery-Operationen waren erfolgreich.',
    'health_failed_title' => 'Mindestens eine für die Anwendung benötigte ServerQuery-Operation ist fehlgeschlagen.',
    'health_last_error' => 'Letzter Laufzeitfehler',
    'health_last_error_resolved' => 'Letzter Laufzeitfehler (inzwischen erfolgreich geprüft)',
    'health_open_details' => 'Details zum Health-Check öffnen',
    'health_modal_title' => 'Health-Check: :virtualserver_name',
    'health_modal_close' => 'Schließen',
    'health_required_permissions' => 'Benötigte ServerQuery-Rechte',
    'health_pending_title' => 'Für diese Instanz wurde noch kein Health-Check im Hintergrund ausgeführt.',
    'health_last_checked_at' => 'Letzter Health-Check: :time',
    'health_problem_since' => 'Problem besteht seit: :time',
    'bot_restart_scheduled' => 'Bot-Neustart geplant',
    'bot_restart_scheduled_at' => 'Neustart geplant für: :time',
    'bot_restart_reason' => 'Grund',
    'health_check_connection' => 'ServerQuery-Verbindung und Anmeldung',
    'health_check_channels' => 'Kanäle lesen',
    'health_check_clients' => 'Clients lesen',
    'health_check_servergroups' => 'Servergruppen lesen',
    'health_check_servergroup_members' => 'Mitglieder der Servergruppen lesen',
    'health_check_serverinfo' => 'Virtuellen Server lesen',
    'health_check_connectioninfo' => 'Verbindungsstatistiken lesen',
    'health_check_events' => 'Server-Events abonnieren',

];
