<?php

return [

    /**
     * Banner Configuration Headline
     */
    'banner_configuration' => 'Banner Konfiguration',

    /**
     * Banner Configuration Buttons
     */
    'back_button' => 'Zurück',
    'test_redirect_button' => 'Weiterleitung testen',
    'add_row_button' => 'Zeile hinzufügen',
    'save_button' => 'Speichern',

    /**
     * Previews
     */
    'preview_with_grid_system' => 'Vorschau mit Gittersystem',
    'preview_without_grid_system' => 'Vorschau',

    /**
     * About Grid System
     */
    'about_grid_system' => 'Über das Gittersystem',
    'grid_system_purpose' => 'Mit Hilfe des Gittersystems kannst du schneller erkennen, welche X-Y-Koordinaten du benötigst, um deinen Text an die richtige Stelle zu setzen.',
    'get_x_y_coordinates_on_click' => 'Durch das klicken auf eine Position auf einem der Bilder, erhältst du die entsprechenden Koordinaten.',
    'grid_system_explanation' => 'Jede horizontale und vertikale Linie des Rastersystems entspricht 25px. In der linken oberen Ecke, beträgt X und Y jeweils 0px.',
    'x_coordinate' => 'X-Koordinate (horizontal)',
    'y_coordinate' => 'Y-Koordinate (vertikal)',

    /**
     * Accordion Status
     */
    'accordion_status_not_configured' => 'Unkonfiguriert',
    'accordion_status_configured' => 'Konfiguriert',
    'accordion_status_no_configurations' => 'Keine Konfigurationen',
    'accordion_status_has_configurations' => '{1} :count_configurations Konfiguration|{2,*} :count_configurations Konfigurationen',

    /**
     * Accordions
     */
    'name_accordion_headline' => 'Name',
    'name_placeholder' => 'z.B. Event Ankündigung',
    'name_help' => 'Worum wird es in dieser Banner Konfiguration gehen? Gib ihr einen beschreibenden Namen.',

    'redirect_url_accordion_headline' => 'URL-Weiterleitung',
    'redirect_url_hostbanner_url' => 'Für diese Funktionalität musst du die folgende URL als Hostbanner URL auf deinem TeamSpeak Server konfigurieren: <code>:hostbanner_url</code>',
    'redirect_url_placeholder' => 'z.B. https://example.com/neuigkeiten',
    'redirect_url_help' => 'Eine optionale URL, an die der Benutzer weitergeleitet werden soll, wenn er auf den Banner klickt. Standardmäßig wird die gerenderte Vorlage geöffnet.',

    'disable_at_accordion_headline' => 'Automatische Deaktivierung',
    'disable_at_use_case' => 'Diese Funktion ist zum Beispiel nützlich, wenn du auf deinem Banner eine Veranstaltung für ein bestimmtes Datum (und eine bestimmte Uhrzeit) ankündigst. Wenn du hier das entsprechende Datum (und die Uhrzeit) einstellst, wird der dynamische Banner diese konfigurierte Vorlage anschließend automatisch für dich deaktivieren, so dass du sie nicht manuell deaktivieren musst.',
    'disable_at_help' => 'Definiere ein optionales Datum und eine Uhrzeit, zu der diese Konfiguration automatisch deaktiviert werden soll. Lass das Feld leer, um sie nicht automatisch zu deaktivieren.',

    'text_configurations_accordion_headline' => 'Text Konfigurationen',

    /**
     * Form Validation
     */
    'form_validation_looks_good' => 'Sieht gut aus!',
    'name_validation_error' => 'Bitte gib einen gültigen Name an!',
    'redirect_url_validation_error' => 'Bitte gib eine gültige URL an!',
    'disable_at_validation_error' => 'Bitte gib einen gültigen Zeitpunkt an!',

];
