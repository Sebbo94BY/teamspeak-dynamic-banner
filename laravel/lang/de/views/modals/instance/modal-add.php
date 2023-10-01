<?php

return [

    /**
     * Modal Headline
     */
    'add_instance' => 'Instanz hinzufügen',

    /**
     * Form
     */
    'host' => 'Host',
    'host_placeholder' => 'z.B. my.teamspeak.local oder 192.168.2.87',
    'host_help' => 'Der Hostname, die Domain oder die IP-Adresse des TeamSpeak Servers.',

    'voice_port' => 'Voice Port',
    'voice_port_placeholder' => 'z.B. 9987',
    'voice_port_help' => 'Der Voice Port des TeamSpeak Servers, zu dem die Verbindung hergestellt werden soll.',

    'serverquery_port' => 'ServerQuery Port',
    'serverquery_port_placeholder' => 'z.B. 10022',
    'serverquery_port_help' => 'Der Serverquery Port des TeamSpeak Servers zum Ausführen von Befehlen und sammeln von Daten.',

    'serverquery_encryption' => 'Aktiviere ServerQuery Verschlüsselung (SSH)',
    'serverquery_encryption_php_extension_ssh_unavailable' => 'Die PHP Erweiterung ssh2 ist auf diesem Webserver nicht verfügbar, wird aber für verschlüsselte Verbindungen benötigt.',
    'serverquery_encryption_help' => 'Wenn aktiviert, wird die ServerQuery Verbindung über eine verschlüsselte SSH-Verbindung hergestellt. Der entsprechende ServerQuery Port muss eingestellt werden.',

    'serverquery_username' => 'ServerQuery Benutzername',
    'serverquery_username_placeholder' => 'z.B. serveradmin',
    'serverquery_username_help' => 'Der Serverquery Benutzername für die Authentifizierung.',

    'serverquery_password' => 'ServerQuery Passwort',
    'serverquery_password_placeholder' => 'z.B. geheimesPasswort',
    'serverquery_password_help' => 'Das Passwort des zuvor definierten ServerQuery Benutzers.',

    'client_nickname' => 'Client Nickname',
    'client_nickname_placeholder' => 'z.B. Dynamischer Banner',
    'client_nickname_help' => 'Wie dieser Client auf dem TeamSpeak Server benannt werden soll. (maximal 30 Zeichen)',

    /**
     * Buttons
     */
    'dismiss_button' => 'Abbrechen',
    'add_button' => 'Hinzufügen',

    /**
     * Form Validation
     */
    'form_validation_looks_good' => 'Sieht gut aus!',
    'host_validation_error' => 'Bitte gib einen gültigen Hostnamen, eine Domain oder eine IP-Adresse an!',
    'voice_port_validation_error' => 'Bitte gib einen gültigen Voice Port an!',
    'serverquery_port_validation_error' => 'Bitte gib einen gültigen ServerQuery Port an!',
    'serverquery_encryption_validation_error' => 'Du kannst diese Checkbox nur aktivieren oder deaktivieren!',
    'serverquery_username_validation_error' => 'Bitte gib einen gültigen ServerQuery Benutzernamen an!',
    'serverquery_password_validation_error' => 'Bitte gib ein gültiges ServerQuery Passwort an!',
    'client_nickname_validation_error' => 'Bitte gib einen gültigen Client Nickname an!',

];
