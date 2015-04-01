<?php

return array(
    'questionary' => [
        'title' => [
            'CODE_NEW_CUSTOMER_EMAIL' => 'Bitte gib deine aktuelle E-Mail Adresse ein um die Registrierung abzuschließen:',
            'CODE_LOGIN_OR_NEW_ACCOUNT' => 'Möchtest du dich in dein bestehendes Konto einloggen oder ein neues Konto erstellen?',
            'CODE_MERGE_OR_NEW_ACCOUNT' => 'Möchtest du dein bestehendes Konto von einem anderen Service benutzen, oder ein neues Konto erstellen?',
            'CODE_CONFIRM_ACCOUNT_MERGE_PASSWORD' => 'Möchtest du deine Konten zusammenführen?',
            'CODE_CONFIRM_ACCOUNT_MERGE_SIMPLE' => 'Möchtest du deine Konten zusammenführen?',
        ],
    ],
    'questions' => [
        'text'  => [
            'CODE_NEW_CUSTOMER_EMAIL_EMAIL' => 'Deine E-Mail Adresse:',
            'CODE_LOGIN_OR_NEW_ACCOUNT_EMAIL' => 'Du kannst eine neue E-Mail Adresse angeben:',
            'CODE_LOGIN_OR_NEW_ACCOUNT_PASSWORD' => 'Oder dein Passwort für dein bestehendes Konto:',
            'CODE_MERGE_OR_NEW_ACCOUNT_SELECT_SERVICE' =>'Möchtest du ein neues Konto erstellen oder dein bestehendes Konto benutzen?',
            'CODE_CONFIRM_MERGE_ACCOUNT_YES_OR_NO' => 'Möchtest du dein neues Konto mit deinem bestehenden Konto zusammenführen?',
            'CODE_CONFIRM_MERGE_ACCOUNT_PASSWORD' => 'Um deine Konten zusammenzuführen musst du dein Passwort angeben:'
        ],
        'select' => [
            'CODE_MERGE_OR_NEW_ACCOUNT_SELECT_SERVICE' => [
                'selection_bid' => 'Bitte wähle:',
                'new_account'   => 'Neues Konto erstellen',
            ],
            'CODE_CONFIRM_MERGE_ACCOUNT_YES_OR_NO' => [
                'selection_bid' => 'Bitte wähle:',
                'yes'   => 'Ja',
                'no'    => 'Nein',
            ],
        ],
        'special' => [
            'CODE_LOGIN_OR_NEW_ACCOUNT_PASSWORD' => 'Oder gib dein Passwort zu deinem bestehenden Konto an(E-Mail: %email%):',
            'CODE_CONFIRM_MERGE_ACCOUNT_YES_OR_NO' => 'Möchtest du dein neues Konto bei {requestingService} mit deinem bestehenden Konto bei {confirmingService} verknüpfen (mit der E-Mail %email%)?',
            'CODE_CONFIRM_MERGE_ACCOUNT_PASSWORD' => 'Wenn du deine Konten zusammenführen möchtest, musst du das Passwort deines Kontos bei {confirmingService} mit der E-Mail Adresse %email% angeben.',
        ],
    ],
);