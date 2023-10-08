<?php

return [

    /**
     * Twitch Headline
     */
    'twitch' => 'Twitch',

    /**
     * Accordion
     */
    'api_credentials_accordion_headline' => 'Twitch API Zugangsdaten',

    'api_credentials_accordion_unconfigured' => 'Unkonfiguriert',
    'api_credentials_accordion_invalid_credentials' => 'Ungültige Zugangsdaten',
    'api_credentials_accordion_valid_credentials' => 'Gültige Zugangsdaten',

    'api_usage_information' => 'Um diese Integration nutzen zu können, musst du diese Anwendung als Anwendung bei Twitch registrieren, damit du API-Zugangsdaten erhältst.',
    'twitch_register_app_documentation' => 'Hier findest du die ausführliche offizielle Dokumentation: <a href="https://dev.twitch.tv/docs/authentication/register-app">https://dev.twitch.tv/docs/authentication/register-app</a>',
    'installation_instructions' => 'Kurzanleitung',
    'step_login_or_create_twitch_account' => 'Melde dich bei deinem Twitch-Konto an oder erstelle ein neues, falls du noch keins hast.',
    'step_open_twitch_dev_console' => 'Öffne <a href="https://dev.twitch.tv/console">https://dev.twitch.tv/console</a>',
    'step_open_applications' => 'Klicke auf <b>Anwendungen</b>',
    'step_open_register_app' => 'Klicke auf <b>Deine Anwendung registrieren</b>',
    'step_fill_out_the_form' => 'Fülle das Formular aus',
    'step_fill_out_the_form_name' => '<b>Name</b>: <code>teamspeak-dynamischer-banner</code> (nur ein Beispiel; benutze jeden Namen, den du willst - er ist nur auf dieser Twitch-Seite sichtbar)',
    'step_fill_out_the_form_oauth_redirect_urls' => '<b>OAuth Redirect URLs</b>: <code>http://localhost</code> (nicht relevant, also setze dies einfach zum Beispiel)',
    'step_fill_out_the_form_category' => '<b>Kategorie</b>: <code>Website Integration</code>',
    'step_open_new_app' => 'Klicke auf <b>Verwalten</b> neben deiner neuen Anwendung',
    'step_copy_and_insert_client_id' => 'Afterwards, you will see the <code>Client-ID</code> of your application. Insert this value into the <b>Client ID</b> field here.',
    'step_copy_and_insert_client_secret' => 'Klicke auf <b>Neues Geheimnis</b>. Du wirst temporär das <code>Client-Geheimnis</code> deiner Anwendung sehen. Füge diesen Wert in das <b>Client Geheimnis</b> Feld hier ein.',
    'step_submit_form' => 'Sende das Formular ab',

    'api_client_id' => 'Client ID',
    'api_client_id_placeholder' => 'z.B. 1ec8e09a145fc972b5eed9d1deb51631',
    'api_client_id_help' => 'Die Client ID deiner Twitch API Zugangsdaten.',

    'api_client_secret' => 'Client Geheimnis',
    'api_client_secret_placeholder' => 'z.B. ca733c04c302365cc782283ed5b7d39a',
    'api_client_secret_help' => 'Das Client Geheimnis deiner Twitch API Zugangsdaten.',

    /**
     * Buttons
     */
    'save_button' => 'Speichern',
    'delete_api_credentials_button' => 'API Zugangsdaten löschen',
    'add_streamer_button' => 'Twitch Streamer hinzufügen',

    /**
     * Information Box
     */
    'no_permissions_to_edit_the_api_credentials' => 'Du hast keine Berechtigungen, die API Zugangsdaten einzusehen und zu ändern. Bitte wende dich an einen Admin.',
    'no_streamer_added_info' => 'Bisher wurde noch kein Twitch Streamer hinzugefügt.',
    'no_twitch_api_credentials_are_configured' => 'Diese Integration funktioniert nur, wenn du gültige Twitch API Zugangsdaten konfigurierst. Diese Integration zieht derzeit weder Twitch Stream Informationen, noch aktiviert oder deaktiviert sie deine Bannervorlagen, wenn sie konfiguriert sind.',

    /**
     * Datatable
     */
    'table_stream_status' => 'Status',
    'table_stream_url' => 'Stream URL',
    'table_last_modified' => 'Zuletzt geändert',

    'table_stream_status_online' => 'Online',
    'table_stream_status_offline' => 'Offline',

    /**
     * Form Validation
     */
    'form_validation_looks_good' => 'Sieht gut aus!',
    'api_client_id_validation_error' => 'Bitte gib eine gültige Client ID an!',
    'api_client_secret_validation_error' => 'Bitte gib einen gültigen Client Secret an!',

];
