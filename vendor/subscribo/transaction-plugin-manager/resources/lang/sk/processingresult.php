<?php
/**
 * Slovak language resource file for TransactionProcessingResultBase
 */
use Subscribo\TransactionPluginManager\Interfaces\TransactionProcessingResultInterface as ProcessingResult;

return [
    'messages' => [
        'fallback' => [
            ProcessingResult::STATUS_SUCCESS => 'Ďakujeme za Vašu objednávku',
            ProcessingResult::STATUS_WAITING => 'Spracovanie finančného prevodu čaká',
            ProcessingResult::STATUS_ERROR => 'Počas spracovania finančného prevodu sa stala nejaká chyba',
            ProcessingResult::STATUS_FAILURE => 'Spracovanie finančného prevodu zlyhalo',
        ],
        'specific' => [
            ProcessingResult::STATUS_WAITING => [
                ProcessingResult::WAITING_FOR_CUSTOMER_INPUT => 'Prosím, dokončite vstup Vašich údajov',
                ProcessingResult::WAITING_FOR_CUSTOMER_CONFIRMATION => 'Prosím, potvrďte finančný prevod (pokiaľ je to potrebné, skontrolujte svoju e-mailovú schránku)',
                ProcessingResult::WAITING_FOR_MERCHANT_INPUT => 'Je možné, že je potrebné počkať, kým bude finančný prevod úplne dokončený',
                ProcessingResult::WAITING_FOR_MERCHANT_CONFIRMATION => 'Je možné, že je potrebné počkať, kým bude finančný prevod úplne dokončený',
                ProcessingResult::WAITING_FOR_MERCHANT_PROCESSING => 'Je možné, že je potrebné počkať, kým bude finančný prevod úplne dokončený',
                ProcessingResult::WAITING_FOR_MERCHANT_DECISION => 'Je možné, že je potrebné počkať, kým bude finančný prevod úplne dokončený',
                ProcessingResult::WAITING_FOR_GATEWAY_PROCESSING => 'Je možné, že je potrebné počkať, kým bude finančný prevod úplne dokončený',
                ProcessingResult::WAITING_FOR_THIRD_PARTY => 'Je možné, že je potrebné počkať, kým bude finančný prevod úplne dokončený',
            ],
            ProcessingResult::STATUS_FAILURE => [
                ProcessingResult::FAILURE_UNSPECIFIED => 'Spracovanie finančného prevodu zlyhalo',
                ProcessingResult::FAILURE_DENIED => 'Prosím, skúste platbu iným spôsobom',
                ProcessingResult::FAILURE_INSUFFICIENT_FUNDS => 'Vaša karta / účet nedisponuje dostatočnými prostriedkami pre túto platbu',
                ProcessingResult::FAILURE_LIMIT_EXCEEDED => 'Vaša karta / účet nemá dostatočný limit pre túto platbu',
                ProcessingResult::FAILURE_CARD_BLOCKED => 'Vaša karta je zablokovaná',
                ProcessingResult::FAILURE_CARD_EXPIRED => 'Platnosť Vašej karty skončila',
                ProcessingResult::FAILURE_CARD_NOT_ACTIVATED => 'Vaša karta nie je aktivovaná pre platby na internete',
            ],
            ProcessingResult::STATUS_ERROR => [
                ProcessingResult::ERROR_INPUT => 'Prosím, skontrolujte a opravte svoj vstup',
                ProcessingResult::ERROR_CONNECTION => 'Pri pokuse o komunikáciu s platobnou bránou sa vyskytla chyba. Prosím, skúste platbu iným spôsobom alebo zopakujte pokus neskôr.',
                ProcessingResult::ERROR_RESPONSE => 'Pri pokuse o komunikáciu s platobnou bránou sa vyskytla chyba. Prosím, skúste platbu iným spôsobom alebo zopakujte pokus neskôr.',
                ProcessingResult::ERROR_GATEWAY => 'Platobná brána nie je v tejto chvíli schopná spracovať Vašu požiadavku. Prosím, skúste platbu iným spôsobom alebo zopakujte pokus neskôr.',
                ProcessingResult::ERROR_SERVER => 'Keď sme sa pokúsili spracovať Vašu platbu, vyskytla sa nejaká chyba.  Prosím, skúste platbu iným spôsobom alebo zopakujte pokus neskôr.',
            ],
        ],
        'add' => [
            'transferred' => 'Prosím, kontaktujte našu službu podpory pre vrátenie peňazí alebo zrušenie platby či faktúry.',
            'possibly_transferred' => 'Prosím, kontaktujte našu službu podpory pre zistenie stavu finančného prevodu a pre prípadné vrátenie peňazí alebo zrušenie platby či faktúry.',
            'reserved' => 'Prosím, kontaktujte našu službu podpory pre zrušenie rezervácie peňazí na Vašom účte alebo karte.',
            'possibly_reserved' => 'Prosím, kontaktujte našu službu podpory ak je potrebné zrušenie rezervácie peňazí na Vašom účte alebo karte.',
            'undefined' => 'Finančný prevod mohol ale nemusel prebehnúť. Prosím, kontaktujte našu službu podpory pre zistenie aktuálného stavu.',
        ]
    ],
];
