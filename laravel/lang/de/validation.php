<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => ':attribute muss akzeptiert werden.',
    'accepted_if' => ':attribute muss akzeptiert werden, wenn :other :value ist.',
    'active_url' => ':attribute ist keine gültige URL.',
    'after' => ':attribute muss ein Datum nach dem :date sein.',
    'after_or_equal' => ':attribute muss ein Datum nach oder gleich dem :date sein.',
    'alpha' => ':attribute darf nur Buchstaben beinhalten.',
    'alpha_dash' => ':attribute darf nur Buchstaben, Zahlen, Bindestriche und Unterstriche beinhalten.',
    'alpha_num' => ':attribute darf nur Buchstaben und Zahlen beinhalten.',
    'array' => ':attribute muss ein Array sein.',
    'ascii' => ':attribute darf nur alphanumerische Zeichen und Symbole mit einem Byte enthalten.',
    'before' => ':attribute muss ein Datum vor dem :date sein.',
    'before_or_equal' => ':attribute muss ein Datum vor oder gleich dem :date sein.',
    'between' => [
        'array' => ':attribute muss zwischen :min und :max Elemente haben.',
        'file' => ':attribute muss zwischen :min und :max Kilobytes sein.',
        'numeric' => ':attribute muss zwischen :min und :max sein.',
        'string' => ':attribute muss zwischen :min und :max Zeichen haben.',
    ],
    'boolean' => 'Das Feld :attribute muss true oder falsch sein.',
    'confirmed' => ':attribute Bestätigung stimmt nicht überein.',
    'current_password' => 'Das Passwort ist falsch.',
    'date' => ':attribute ist kein gültiges Datum.',
    'date_equals' => ':attribute muss ein Datum gleich dem :date sein.',
    'date_format' => 'Das :attribute muss dem Format :format entsprechen.',
    'decimal' => ':attribute muss :decimal Dezimalstellen haben.',
    'declined' => ':attribute muss abgelehnt werden.',
    'declined_if' => ':attribute muss abgelehnt werden, wenn :other :value entspricht.',
    'different' => ':attribute und :other müssen unterschiedlich sein.',
    'digits' => ':attribute muss :digits Ziffern sein.',
    'digits_between' => ':attribute muss zwischen :min und :max Ziffern sein.',
    'dimensions' => ':attribute hat ungültige Bild Dimensionen.',
    'distinct' => 'Das Feld :attribute hat einen doppelten Wert.',
    'doesnt_end_with' => 'Das Feld :attribute darf nicht mit einem der folgenden Werte enden: :values',
    'doesnt_start_with' => 'Das Feld :attribute darf nicht mit einem der folgenden Werte beginnen: :values',
    'email' => ':attribute muss eine gültige E-Mail Adresse sein.',
    'ends_with' => 'Das Feld :attribute muss mit einem der folgenden Werte enden: :values',
    'enum' => 'Der ausgewählte Wert :attribute ist ungültig.',
    'exists' => 'Der ausgewählte Wert :attribute ist ungültig.',
    'file' => ':attribute muss eine Datei sein.',
    'filled' => 'Das Feld :attribute muss einen Wert haben.',
    'gt' => [
        'array' => 'The :attribute must have more than :value items.',
        'file' => 'The :attribute must be greater than :value kilobytes.',
        'numeric' => 'The :attribute must be greater than :value.',
        'string' => 'The :attribute must be greater than :value characters.',
    ],
    'gte' => [
        'array' => ':attribute muss mehr als :value Elemente haben.',
        'file' => ':attribute muss größer als :value Kilobytes sein.',
        'numeric' => ':attribute muss größer als :value sein.',
        'string' => ':attribute muss länger als :value Zeichen sein.',
    ],
    'image' => ':attribute muss ein Bild sein.',
    'in' => 'Der gewählte Wert für :attribute ist ungültig.',
    "in_array" => "Der gewählte Wert für :attribute kommt nicht in :other vor.",
    "integer" => ":Attribute muss eine ganze Zahl sein.",
    "ip" => ":Attribute muss eine gültige IP-Adresse sein.",
    "ipv4" => ":Attribute muss eine gültige IPv4-Adresse sein.",
    "ipv6" => ":Attribute muss eine gültige IPv6-Adresse sein.",
    "json" => ":Attribute muss ein gültiger JSON-String sein.",
    'lowercase' => ':attribute muss in Kleinbuchstaben sein.',
    'lt' => [
        'array' => ':attribute muss weniger als :value Elemente haben.',
        'file' => ':attribute muss kleiner als :value Kilobytes sein.',
        'numeric' => ':attribute muss kleiner als :value sein.',
        'string' => ':attribute muss kürzer als :value Zeichen sein.',
    ],
    'lte' => [
        'array' => ':attribute darf maximal :value Elemente haben.',
        'file' => 'The :attribute muss kleiner oder gleich :value Kilobytes sein.',
        'numeric' => ':attribute muss kleiner oder gleich :value sein.',
        'string' => ':attribute darf maximal :value Zeichen lang sein.',
    ],
    'mac_address' => ':attribute muss eine gültige MAC-Adresse sein.',
    'max' => [
        'array' => ':attribute darf maximal :max Elemente haben.',
        'file' => ':attribute darf maximal :max Kilobytes groß sein.',
        'numeric' => ':attribute darf maximal :max sein.',
        'string' => ':attribute darf maximal :max Zeichen haben.',
    ],
    'max_digits' => ':attribute darf maximal :max Ziffern lang sein.',
    'mimes' => ':attribute muss einer der folgenden Dateitypen sein: :values',
    'mimetypes' => ':attribute muss einer der folgenden Dateitypen sein: :values',
    'min' => [
        'array' => ':attribute muss mindestens :min Elemente haben.',
        'file' => ':attribute muss mindestens :min Kilobytes groß sein.',
        'numeric' => ':attribute muss mindestens :min sein.',
        'string' => ':attribute muss mindestens :min Zeichen lang sein.',
    ],
    'min_digits' => ':attribute muss mindestens :min Ziffern lang sein.',
    'missing' => 'Das Feld :attribute muss fehlen.',
    'missing_if' => 'Das Feld :attribute muss fehlen, wenn :other gleich :value ist.',
    'missing_unless' => 'Das Feld :attribute muss fehlen, es sei denn, :other ist :value.',
    'missing_with' => 'Das Feld :attribute muss fehlen, wenn :values vorhanden ist.',
    'missing_with_all' => 'Das Feld :attribute muss fehlen, wenn :values vorhanden sind.',
    'multiple_of' => ':attribute muss ein Vielfaches von :value sein.',
    'not_in' => 'Der gewählte Wert für :attribute ist ungültig.',
    'not_regex' => ':attribute hat ein ungültiges Format.',
    'numeric' => ':attribute muss eine Zahl sein.',
    'password' => [
        'letters' => ':attribute muss mindestens einen Buchstaben beinhalten.',
        'mixed' => ':attribute muss mindestens einen Großbuchstaben und einen Kleinbuchstaben beinhalten.',
        'numbers' => ':attribute muss mindestens eine Zahl beinhalten.',
        'symbols' => ':attribute muss mindestens ein Sonderzeichen beinhalten.',
        'uncompromised' => ':attribute wurde in einem Datenleck gefunden. Bitte wähle ein anderes :attribute.',
    ],
    'present' => ':attribute muss vorhanden sein.',
    'prohibited' => ':attribute ist unzulässig.',
    'prohibited_if' => ':attribute ist unzulässig, wenn :other :value ist.',
    'prohibited_unless' => ':attribute ist unzulässig, wenn :other nicht :values ist.',
    'prohibits' => ':attribute verbietet die Angabe von :other.',
    'regex' => ':attribute Format ist ungültig.',
    'required' => ':attribute muss ausgefüllt werden.',
    'required_array_keys' => 'Das Feld :attribute muss Einträge für folgende Werte enthalten: :values',
    'required_if' => ':attribute muss ausgefüllt werden, wenn :other den Wert :value hat.',
    'required_if_accepted' => ':attribute muss ausgefüllt werden, wenn :other gewählt ist.',
    'required_unless' => ':attribute muss ausgefüllt werden, wenn :other nicht den Wert :values hat.',
    'required_with' => ':attribute muss ausgefüllt werden, wenn :values ausgefüllt wurde.',
    'required_with_all' => ':attribute muss ausgefüllt werden, wenn :values ausgefüllt wurde.',
    'required_without' => ':attribute muss ausgefüllt werden, wenn :values nicht ausgefüllt wurde.',
    'required_without_all' => ':attribute muss ausgefüllt werden, wenn keines der Felder :values ausgefüllt wurde.',
    'same' => ':attribute und :other müssen übereinstimmen.',
    'size' => [
        'array' => ':attribute muss genau :size Elemente haben.',
        'file' => ':attribute muss :size Kilobyte groß sein.',
        'numeric' => ':attribute muss gleich :size sein.',
        'string' => ':attribute muss :size Zeichen lang sein.',
    ],
    'starts_with' => ':attribute muss mit einem der folgenden Anfänge aufweisen: :values',
    'string' => ':attribute muss ein String sein.',
    'timezone' => ':attribute muss eine gültige Zeitzone sein.',
    'unique' => ':attribute ist bereits vergeben.',
    'uploaded' => ':attribute konnte nicht hochgeladen werden.',
    'uppercase' => ':attribute muss in Großbuchstaben sein.',
    'url' => ':attribute muss eine gültige URL sein.',
    'ulid' => ':attribute muss eine gültige ULID sein.',
    'uuid' => ':attribute muss eine gültige UUID sein.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
