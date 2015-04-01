<?php
/**
 * German translation resource file for Validation
 *
 * Taken from Laravel framework (www.laravel.com), modified and translated
 *
 * Translation work in progress ( 0 % )
 *
 * @license MIT
 */
return [
    'validation' => [

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

        "accepted"             => "Das Feld :attribute muss bestätigt werden.",
        "active_url"           => "Das Feld :attribute ist keine gültige URL.",
        "after"                => "Das Feld :attribute muss ein Datum nach dem :date sein.",
        "alpha"                => "Das Feld :attribute darf nur Buchstaben enthalten.",
        "alpha_dash"           => "Das Feld :attribute darf nur Buchstaben, Ziffern und Bindestrichen beinhalten.",
        "alpha_num"            => "Das Feld :attribute darf nur Buchstaben und Ziffer beinhalten.",
        "array"                => "Das Feld :attribute muss ein Array sein.",
        "before"               => "Das Feld :attribute muss ein Datum vor dem :date sein.",
        "between"              => [
            "numeric" => "Das Feld :attribute muss zwischen :min und :max liegen.",
            "file"    => "Das Feld :attribute muss zwischen :min und :max KB haben.",
            "string"  => "Das Feld :attribute muss aus  :min bis :max Zeichen bestehen.",
            "array"   => "Das Feld :attribute muss aus :min bis :max Items bestehen.",
        ],
        "boolean"              => "Das Feld :attribute muss wahr oder falsch sein.",
        "confirmed"            => "Das Feld :attribute wurde nicht bestätigt.",
        "date"                 => "Das Feld :attribute ist kein gültiges Datum.",
        "date_format"          => "Das Feld :attribute entspricht nicht dem Format :format.",
        "different"            => "Die Felder :attribute und :other dürfen nicht gleich sein.",
        "digits"               => "Das Feld :attribute muss aus :digits Ziffern bestehen.",
        "digits_between"       => "Das Feld :attribute muss aus :min bis :max Ziffern bestehen.",
        "email"                => "Das Feld :attribute muss eine gültige E-Mail Adresse beinhalten.",
        "filled"               => "Das Feld :attribute muss ausgefüllt sein.",
        "exists"               => "Das Feld :attribute ist ungültig.",
        "image"                => "Das Feld :attribute muss eine Bilddatei beinhalten.",
        "in"                   => "Das Feld :attribute ist ungültig.",
        "integer"              => "Das Feld :attribute muss eine ganze Zahl sein.",
        "ip"                   => "Das Feld :attribute muss eine gülitge IP-Adresse beinhalten.",
        "max"                  => [
            "numeric" => "Das Feld :attribute muss kleiner als :max sein.",
            "file"    => "Das Feld :attribute darf maximal :max KB groß sein.",
            "string"  => "Das Feld :attribute darf maximal :max Zeichen beinhalten.",
            "array"   => "Das Feld :attribute darf maximal :max Items beinhalten.",
        ],
        "mimes"                => "Das Feld :attribute muss den folgenden Datei-Typ aufweisen: :values.",
        "min"                  => [
            "numeric" => "Das Feld :attribute muss größer sein als :min.",
            "file"    => "Das Feld :attribute muss größer sein als :min KB.",
            "string"  => "Das Feld :attribute muss mindestens :min Zeichen beinhalten.",
            "array"   => "Das Feld :attribute muss mindestens :min Items beinhalten.",
        ],
        "not_in"               => "Ausgewählte(s) :attribute ist ungültig.",
        "numeric"              => "Das Feld :attribute muss eine Zahl sein.",
        "regex"                => "Das Feld :attribute ist ungültig oder ungültig formatiert.",
        "required"             => "Das Feld :attribute muss ausgefüllt sein.",
        "required_if"          => "Das Feld :attribute muss ausgefüllt sein wenn :other gleich :value ist.",
        "required_with"        => "Das Feld :attribute muss ausgefüllt sein wenn :values angegeben ist/sind.",
        "required_with_all"    => "Das Feld :attribute muss ausgefüllt sein wenn :values angegeben ist/sind.",
        "required_without"     => "Das Feld :attribute muss ausgefüllt sein wenn kein(e) :values angegben ist/sind.",
        "required_without_all" => "Das Feld :attribute muss ausgefüllt sein wenn keines der folgenden Felder ausgefüllt ist: :values.",
        "same"                 => "Das Feld :attribute und :other müssen übereinstimmen.",
        "size"                 => [
            "numeric" => "Das Feld :attribute muss genau :size lang sein.",
            "file"    => "Das Feld :attribute muss genau :size KB groß sein.",
            "string"  => "Das Feld :attribute muss aus genau :size Zeichen bestehen.",
            "array"   => "Das Feld :attribute muss aus genau :size Items bestehen.",
        ],
        "unique"               => "Der Wert des Feldes :attribute wurde bereits verwendet.",
        "url"                  => "Das Feld :attribute ist ungültig.",
        "timezone"             => "Das Feld :attribute muss eine Zeitzone beinhalten.",

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
        | The following language lines are used to swap attribute place-holders
        | with something more reader friendly such as E-Mail Address instead
        | of "email". This simply helps us make messages a little cleaner.
        |
        */

        'attributes' => [
            'name' => 'name',
            'email' => 'e-mail',
            'password' => 'password',
            'remember' => 'remember',
            'password_confirmation' => 'password confirmation',
        ],
    ],
];
