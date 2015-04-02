<?php
/**
 * Slovak translation resources file for creating Questionaries and Questions, used by QuestionFactory and QuestionaryFactory
 *
 */
return array(
    // QuestionaryFactory
    'questionary' => [
        'title' => [
            'CODE_NEW_CUSTOMER_EMAIL' => 'Prosím zadajte vami používaný e-mail na dokončenie registrácie:',
            'CODE_LOGIN_OR_NEW_ACCOUNT' => 'Želáte si prihlásiť sa do svojho už existujúceho účtu, alebo si želáte vytvoriť nový účet?',
            'CODE_MERGE_OR_NEW_ACCOUNT' => 'Želáte si použiť váš už existujúci účet, vytvorený pre inú službu, alebo si želáte vytvoriť nový účet pre túto službu?',
            'CODE_CONFIRM_ACCOUNT_MERGE_PASSWORD' => 'Želáte si spojiť svoje účty?',
            'CODE_CONFIRM_ACCOUNT_MERGE_SIMPLE' => 'Želáte si spojiť svoje účty?',
        ],
    ],
    // QuestionFactory
    'questions' => [
        'text'  => [
            'CODE_NEW_CUSTOMER_EMAIL_EMAIL' => 'Vami používaný e-mail:',
            'CODE_LOGIN_OR_NEW_ACCOUNT_EMAIL' => 'Môžte buď zadať nový e-mail:',
            'CODE_LOGIN_OR_NEW_ACCOUNT_PASSWORD' => 'Alebo zadať heslo k vášmu existujúcemu účtu:',
            'CODE_MERGE_OR_NEW_ACCOUNT_SELECT_SERVICE' => 'Želáte si spojiť váš účet s vašim účtom u niektorej z týchto služieb, alebo si želáte vytvoriť nový účet pre túto službu?',
            'CODE_CONFIRM_MERGE_ACCOUNT_YES_OR_NO' => 'Želáte si spojiť svoj nový účet s vaším existujúcim účtom?',
            'CODE_CONFIRM_MERGE_ACCOUNT_PASSWORD' => 'Ak si želáte spojiť účty, prosím zadajte heslo k vášmu účtu u tejto služby:'
            ],
        'select' => [
            'CODE_MERGE_OR_NEW_ACCOUNT_SELECT_SERVICE' => [
                    'selection_bid' => 'Prosím vyberte',
                    'new_account'   => 'Vytvoriť nový účet',
                ],
            'CODE_CONFIRM_MERGE_ACCOUNT_YES_OR_NO' => [
                'selection_bid' => 'Prosím vyberte',
                    'yes'   => 'áno',
                    'no'    => 'nie',
                ],
            ],
        'special' => [
            'CODE_LOGIN_OR_NEW_ACCOUNT_PASSWORD' => 'Alebo zadať heslo k vášmu existujúcemu účtu (email: %email%):',
            'CODE_CONFIRM_MERGE_ACCOUNT_YES_OR_NO' => 'Želáte si spojiť svoj nový účet u služby {requestingService} s vaším existujúcim účtom u služby {confirmingService} (s emailom: %email%)?',
            'CODE_CONFIRM_MERGE_ACCOUNT_PASSWORD' => 'Ak si želáte spojiť účty, prosím zadajte heslo k vášmu účtu s emailom: %email% u služby {confirmingService}:',
        ],
        'attributeNames' => [
            'CODE_NEW_CUSTOMER_EMAIL_EMAIL' => 'e-mail',
            'CODE_LOGIN_OR_NEW_ACCOUNT_EMAIL' => 'e-mail',
            'CODE_LOGIN_OR_NEW_ACCOUNT_PASSWORD' => 'heslo',
            'CODE_MERGE_OR_NEW_ACCOUNT_SELECT_SERVICE' => 'vyberte službu',
            'CODE_CONFIRM_MERGE_ACCOUNT_YES_OR_NO' => 'váš výber',
            'CODE_CONFIRM_MERGE_ACCOUNT_PASSWORD' => 'heslo',
        ],
        'validationMessages' => [
            'CODE_NEW_CUSTOMER_EMAIL_EMAIL' => [
                'required' => 'Pole e-mail field je povinné',
                'email' => 'Zadaný e-mail nie je platný',
            ],
            'CODE_LOGIN_OR_NEW_ACCOUNT_EMAIL' => [
                'required_without' => 'Pole e-mail je povinné, ak nie je zadané heslo',
                'email' => 'Zadaný e-mail nie je platný',
            ],
            'CODE_LOGIN_OR_NEW_ACCOUNT_PASSWORD' => 'Prosím, zadajte heslo k vašemu súčasnému účtu, alebo zadajte nový e-mail',
            'CODE_MERGE_OR_NEW_ACCOUNT_SELECT_SERVICE' => 'Prosím, vyberte službu, alebo možnosť "Vytvoriť nový účet"',
            'CODE_CONFIRM_MERGE_ACCOUNT_YES_OR_NO' => 'Prosím, zvoľte, či si želáte spojiť účty alebo nie',
            'CODE_CONFIRM_MERGE_ACCOUNT_PASSWORD' => 'Ak si želáte spojiť vaše účty, pole heslo je povinné'
        ],
    ],

);
