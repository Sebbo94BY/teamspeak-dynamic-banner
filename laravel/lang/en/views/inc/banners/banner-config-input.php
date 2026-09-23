<?php

return [

    /**
     * Buttons
     */
    'delete_row_button' => 'Delete row',
    'remove_row_button' => 'Remove row',

    /**
     * Form
     */
    'x_coordinate' => 'X-Coordinate (horizontal)',
    'x_coordinate_placeholder' => 'e. g. 30',
    'x_coordinate_help' => 'The anchor point for the horizontal text alignment.',

    'internal_field_label' => 'Internal field label',
    'internal_field_label_placeholder' => 'e. g. Ping or latency',
    'internal_field_label_help' => 'Optional internal name for this text row. This value is not displayed on the banner.',

    'text_alignment' => 'Horizontal alignment',
    'text_alignment_left' => 'Left',
    'text_alignment_center' => 'Center',
    'text_alignment_right' => 'Right',
    'text_alignment_help' => 'Choose whether the X coordinate marks the left edge, center, or right edge of the text.',

    'y_coordinate' => 'Y-Coordinate (vertical)',
    'y_coordinate_placeholder' => 'e. g. 60',
    'y_coordinate_help' => 'The Y-Coordinate, at which position the text should start.',

    'text' => 'Text',
    'text_placeholder' => 'e. g. %VIRTUALSERVER_TOTAL_PING% ms',
    'text_help' => 'Use %VARIABLE% for a value, $(%VALUE_A% - %VALUE_B%) for arithmetic, or $format(%VARIABLE%, "000") to format a number. Click the variables button above for available values.',

    'font_id' => 'Font',
    'font_id_help' => 'Please select the font for this specific text.',

    'font_size' => 'Font Size',
    'font_size_placeholder' => 'e. g. 25',
    'font_size_help' => 'The font size of the text, which should get printed to the template.',

    'font_angle' => 'Font Angle',
    'font_angle_placeholder' => 'e. g. 0',
    'font_angle_help' => 'The font angle between 0 and 360 degree in which the text should get printed to the template.',

    'font_color_in_hexadecimal' => 'Font Color',
    'font_color_in_hexadecimal_help' => 'Define the text color in which your text should be printed.',

    /**
     * Form Validation
     */
    'form_validation_looks_good' => 'Looks good!',
    'x_coordinate_validation_error' => 'Please provide a valid X-Coordinate!',
    'internal_field_label_validation_error' => 'Please provide a valid internal field label!',
    'text_alignment_validation_error' => 'Please select a valid horizontal alignment!',
    'y_coordinate_validation_error' => 'Please provide a valid Y-Coordinate!',
    'text_validation_error' => 'Please provide a valid text!',
    'font_id_validation_error' => 'Please select an available font!',
    'font_size_validation_error' => 'Please provide a valid font size!',
    'font_angle_validation_error' => 'Please provide a valid font angle!',
    'font_color_in_hexadecimal_validation_error' => 'Please provide a valid hexadecimal code for the font color!',

];
