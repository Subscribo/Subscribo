<?php
/**
 * Slovak translation resource file for Validation
 *
 * Taken from Laravel framework (www.laravel.com), modified and translated
 *
 * @license MIT
 */
return [
    "validation" => [
        "accepted"             => "Pole :attribute musí byť prijaté.",
        "active_url"           => "Pole :attribute nie je funkčná URL adresa.",
        "after"                => "Pole :attribute musí byť dátum po :date.",
        "alpha"                => "Pole :attribute môže obsahovať iba písmená.",
        "alpha_dash"           => "Pole :attribute môže obsahovať iba písmená, čísla a pomlčky.",
        "alpha_num"            => "Pole :attribute môže obsahovať iba písmená a čísla.",
        "array"                => "Pole :attribute musí byť pole.",
        "before"               => "Pole :attribute musí byť dátum pred :date.",
        "between"              => [
            "numeric" => "Pole :attribute musí byť medzi :min a :max.",
            "file"    => "Pole :attribute musí byť medzi :min a :max kilobytov.",
            "string"  => "Pole :attribute musí mať medzi :min a :max znakov.",
            "array"   => "Pole :attribute musí obsahovať medzi :min a :max položiek.",
        ],
        "boolean"              => "Pole :attribute musí byť áno alebo nie.",
        "confirmed"            => "Potvrdenie poľa :attribute nesúhlasí.",
        "date"                 => "Pole :attribute nie je platný dátum.",
        "date_format"          => "Pole :attribute nie je platný dátum podľa formátu :format.",
        "different"            => "Polia :attribute a :other musia byť odlišné.",
        "digits"               => "Pole :attribute musí obsahovať :digits číslic.",
        "digits_between"       => "Pole :attribute musí obsahovať najmenej :min a najviac :max číslic.",
        "email"                => "Pole :attribute musí byť platná emailová adresa.",
        "filled"               => "Pole :attribute je nutné vyplniť.",
        "exists"               => "Vybraná položka v poli :attribute je neplatná.",
        "image"                => "Pole :attribute musí byť obrázok.",
        "in"                   => "Vybraná položka v poli :attribute je neplatná.",
        "integer"              => "Pole :attribute musí byť celé číslo.",
        "ip"                   => "Pole :attribute musí byť platná IP adresa.",
        "max"                  => [
            "numeric" => "Pole :attribute nemôže byť väčšie ako :max.",
            "file"    => "Pole :attribute nemôže mať viac ako :max kilobytov.",
            "string"  => "Pole :attribute nemôže mať viac ako :max znakov.",
            "array"   => "Pole :attribute nemôže mať viac ako :max položiek.",
        ],
        "mimes"                => "Pole :attribute musí byť súbor typu :values.",
        "min"                  => [
            "numeric" => "Pole :attribute musí byť minimálne :min.",
            "file"    => "Pole :attribute musí mať aspoň :min kilobytov.",
            "string"  => "Pole :attribute musí obsahovať aspoň :min znakov.",
            "array"   => "Pole :attribute musí obsahovať aspoň :min položiek.",
        ],
        "not_in"               => "Vybraná položka v poli :attribute je neplatná.",
        "numeric"              => "Pole :attribute musí byť číslo.",
        "regex"                => "Formát poľa :attribute je neplatný.",
        "required"             => "Pole :attribute je povinné.",
        "required_if"          => "Pole :attribute je povinné keď pole :other je :value.",
        "required_with"        => "Pole :attribute je povinné keď pole :values je zadané.",
        "required_with_all"    => "Pole :attribute je povinné keď polia :values sú zadané.",
        "required_without"     => "Pole :attribute je povinné keď polia :values nie sú zadané.",
        "required_without_all" => "Pole :attribute je povinné keď žiadne z polí :values nie je zadané.",
        "same"                 => "Polia :attribute a :other musia byť rovnaké.",
        "size"                 => [
            "numeric" => "Pole :attribute musí byť :size.",
            "file"    => "Pole :attribute musí mať práve :size kilobytov.",
            "string"  => "Pole :attribute musí obsahovať práve :size znakov.",
            "array"   => "Pole :attribute musí obsahovať práve :size položiek.",
        ],
        "unique"               => "Zadaná hodnota poľa :attribute už bola použitá.",
        "url"                  => "Pole :attribute nemá formát platnej URL.",
        "timezone"             => "Pole :attribute musí byť platná časová zóna.",


        'attributes' => [
            'name' => 'meno',
            'email' => 'e-mail',
            'password' => 'heslo',
            'remember' => 'zapamätať',
            'password_confirmation' => 'kontrola hesla',
        ],
    ],
];
