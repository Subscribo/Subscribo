<?php
/**
 * German translation resources file for creating Questionaries and Questions, used by QuestionFactory and QuestionaryFactory
 * Translation work in progress ( 70 % ) TODO: please translate attributeNames and validationMessages
 */
return array(
    // QuestionaryFactory
    'questionary' => [
        'title' => [
            'CODE_NEW_CUSTOMER_EMAIL' => 'Bitte gib deine aktuelle E-Mail Adresse ein um die Registrierung abzuschließen:',
            'CODE_LOGIN_OR_NEW_ACCOUNT' => 'Möchtest du dich in dein bestehendes Konto einloggen oder ein neues Konto erstellen?',
            'CODE_MERGE_OR_NEW_ACCOUNT' => 'Möchtest du dein bestehendes Konto von einem anderen Service benutzen, oder ein neues Konto erstellen?',
            'CODE_CONFIRM_ACCOUNT_MERGE_PASSWORD' => 'Möchtest du deine Konten zusammenführen?',
            'CODE_CONFIRM_ACCOUNT_MERGE_SIMPLE' => 'Möchtest du deine Konten zusammenführen?',
        ],
    ],
    // QuestionFactory
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
        'attributeNames' => [
            'CODE_NEW_CUSTOMER_EMAIL_EMAIL' => 'e-mail',
            'CODE_LOGIN_OR_NEW_ACCOUNT_EMAIL' => 'e-mail',
            'CODE_LOGIN_OR_NEW_ACCOUNT_PASSWORD' => 'password',
            'CODE_MERGE_OR_NEW_ACCOUNT_SELECT_SERVICE' => 'select service',
            'CODE_CONFIRM_MERGE_ACCOUNT_YES_OR_NO' => 'your choice',
            'CODE_CONFIRM_MERGE_ACCOUNT_PASSWORD' => 'password',
        ],
        'validationMessages' => [
            'CODE_NEW_CUSTOMER_EMAIL_EMAIL' => [
                'required' => 'E-mail field is required',
                'email' => 'Provided e-mail is not valid',
            ],
            'CODE_LOGIN_OR_NEW_ACCOUNT_EMAIL' => [
                'required_without' => 'Your e-mail is necessary, when your password is not provided',
                'email' => 'Provided e-mail is not valid',
            ],
            'CODE_LOGIN_OR_NEW_ACCOUNT_PASSWORD' => 'Please, provide your password to your current account or provide a new email',
            'CODE_MERGE_OR_NEW_ACCOUNT_SELECT_SERVICE' => 'Please, select a service or option "Create a new account"',
            'CODE_CONFIRM_MERGE_ACCOUNT_YES_OR_NO' => 'Please, choose whether you want to merge your accounts or not',
            'CODE_CONFIRM_MERGE_ACCOUNT_PASSWORD' => 'If you want to merge your accounts, password is a required field'
        ],
    ],
);