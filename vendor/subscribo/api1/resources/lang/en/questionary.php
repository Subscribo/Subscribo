<?php
/**
 * English translation resources file for creating Questionaries and Questions, used by QuestionFactory and QuestionaryFactory
 *
 */
return array(
    // QuestionaryFactory
    'questionary' => [
        'title' => [
            'CODE_NEW_CUSTOMER_EMAIL' => 'Please provide your actual email to finish your registration:',
            'CODE_LOGIN_OR_NEW_ACCOUNT' => 'Would you like to login to your existing account or create a new one?',
            'CODE_MERGE_OR_NEW_ACCOUNT' => 'Would you like to use your existing account, created for different service, or to create now a new one?',
            'CODE_CONFIRM_ACCOUNT_MERGE_PASSWORD' => 'Would you like to merge your accounts?',
            'CODE_CONFIRM_ACCOUNT_MERGE_SIMPLE' => 'Would you like to merge your accounts?',
        ],
    ],
    // QuestionFactory
    'questions' => [
        'text'  => [
            'CODE_NEW_CUSTOMER_EMAIL_EMAIL' => 'Your actual email:',
            'CODE_LOGIN_OR_NEW_ACCOUNT_EMAIL' => 'You can either provide a new email:',
            'CODE_LOGIN_OR_NEW_ACCOUNT_PASSWORD' => 'Or provide a password to your existing account:',
            'CODE_MERGE_OR_NEW_ACCOUNT_SELECT_SERVICE' =>'Would you like to merge your account with one of the following services or create a new account?',
            'CODE_CONFIRM_MERGE_ACCOUNT_YES_OR_NO' => 'Would you like to merge your new account with your existing account?',
            'CODE_CONFIRM_MERGE_ACCOUNT_PASSWORD' => 'If you want to merge accounts, please provide a password to your current service:'
            ],
        'select' => [
            'CODE_MERGE_OR_NEW_ACCOUNT_SELECT_SERVICE' => [
                    'selection_bid' => 'Please select',
                    'new_account'   => 'Create a new account',
                ],
            'CODE_CONFIRM_MERGE_ACCOUNT_YES_OR_NO' => [
                    'selection_bid' => 'Please select',
                    'yes'   => 'yes',
                    'no'    => 'no',
                ],
            ],
        'special' => [
            'CODE_LOGIN_OR_NEW_ACCOUNT_PASSWORD' => 'Or provide a password to your existing account (email: %email%):',
            'CODE_CONFIRM_MERGE_ACCOUNT_YES_OR_NO' => 'Would you like to merge your new account by {requestingService} with your existing account by {confirmingService} (with email %email%)?',
            'CODE_CONFIRM_MERGE_ACCOUNT_PASSWORD' => 'If you want to merge accounts, please provide a password to your account by {confirmingService} with email %email%',
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
