<?php

return [

    /**
     * Buttons
     */
    'delete_row_button' => 'Zeile löschen',
    'remove_row_button' => 'Zeile entfernen',

    /**
     * Form
     */
    'x_coordinate' => 'X-Koordinate (horizontal)',
    'x_coordinate_placeholder' => 'z.B. 30',
    'x_coordinate_help' => 'Der Ankerpunkt für die horizontale Ausrichtung des Textes.',

    'internal_field_label' => 'Interne Feldbezeichnung',
    'internal_field_label_placeholder' => 'z.B. Ping oder Latenz',
    'internal_field_label_help' => 'Optionaler interner Name für diese Textzeile, z.B. „Ping“ oder „Latenz“. Dieses Feld wird nicht auf dem Banner angezeigt.',

    'text_alignment' => 'Horizontale Ausrichtung',
    'text_alignment_left' => 'Links',
    'text_alignment_center' => 'Mittig',
    'text_alignment_right' => 'Rechts',
    'text_alignment_help' => 'Legt fest, ob die X-Koordinate den linken Rand, die Mitte oder den rechten Rand des Textes markiert.',

    'y_coordinate' => 'Y-Koordinate (vertikal)',
    'y_coordinate_placeholder' => 'z.B. 60',
    'y_coordinate_help' => 'Die Y-Koordinate, an welcher Position der Text beginnen soll.',

    'text' => 'Text',
    'text_placeholder' => 'z.B. %VIRTUALSERVER_TOTAL_PING% ms',
    'text_help' => 'Nutze %VARIABLE% für einen Wert, $(%WERT_A% - %WERT_B%) für Berechnungen oder $format(%VARIABLE%, "000") zum Formatieren einer Zahl. Über die Schaltfläche für Variablen oben werden alle verfügbaren Werte angezeigt.',

    'font_id' => 'Schriftart',
    'font_id_help' => 'Bitte wähle die Schriftart für diesen bestimmten Text.',

    'font_size' => 'Schriftgröße',
    'font_size_placeholder' => 'z.B. 25',
    'font_size_help' => 'Die Schriftgröße des Textes, der auf die Vorlage gedruckt werden soll.',

    'font_angle' => 'Schriftwinkel',
    'font_angle_placeholder' => 'z.B. 0',
    'font_angle_help' => 'Der Schriftwinkel zwischen 0 und 360 Grad, in dem der Text auf die Vorlage gedruckt werden soll.',

    'font_color_in_hexadecimal' => 'Schriftfarbe',
    'font_color_in_hexadecimal_help' => 'Lege die Textfarbe fest, in der dieser Text gedruckt werden soll.',

    /**
     * Form Validation
     */
    'form_validation_looks_good' => 'Sieht gut aus!',
    'x_coordinate_validation_error' => 'Bitte gib eine gültige X-Koordinate an!',
    'internal_field_label_validation_error' => 'Bitte gib eine gültige interne Feldbezeichnung an!',
    'text_alignment_validation_error' => 'Bitte wähle eine gültige horizontale Ausrichtung aus!',
    'y_coordinate_validation_error' => 'Bitte gib eine gültige Y-Koordinate an!',
    'text_validation_error' => 'Bitte gib einen gültigen Text an!',
    'font_id_validation_error' => 'Bitte wähle eine verfügbare Schriftart aus!',
    'font_size_validation_error' => 'Bitte gib eine gültige Schriftgröße an!',
    'font_angle_validation_error' => 'Bitte gib einen gültigen Schriftwinkel an!',
    'font_color_in_hexadecimal_validation_error' => 'Bitte gib einen gültigen Hexadezimal-Code für die Schriftfarbe an!',

];
