<?php namespace Subscribo\Exception\Exceptions;

//Slovak Translations Resource file for ApiExceptionHandler

return [
    'suggestions' => [
        'specific' => [
            400 => [
                InvalidInputHttpException::DEFAULT_EXCEPTION_CODE => 'Chybové správy pre váš vstup môžete nájsť v poli validationErrors.',
                InvalidIdentifierHttpException::DEFAULT_EXCEPTION_CODE => 'Skontrolujte formát identifikátora ktorý je súčasťou požadovanej URL.',
                InvalidQueryHttpException::DEFAULT_EXCEPTION_CODE => 'Chybové správy pre parametre URL môžete nájsť v poli validationErrors.',
                SessionVariableNotFoundHttpException::DEFAULT_EXCEPTION_CODE => 'Vaša session bola stratená alebo určitá hodnota očakávaná v session nebola nájdená. Toto sa môže stať napríklad keď je za určitých okolností stlačené tlačítko Späť vášho prehliadača.'
            ],
            403 => [
                WrongServiceHttpException::DEFAULT_EXCEPTION_CODE => 'Pokúšate sa pristupovať ku zdrojom patriacim inej službe.',
                WrongAccountHttpException::DEFAULT_EXCEPTION_CODE => 'Pokúšate sa pristupovať k účtu patriacemu inej službe.',
            ],
            404 => [
                InstanceNotFoundHttpException::DEFAULT_EXCEPTION_CODE => 'Skontrolujte ID alebo Identifier požadovaného objektu.',
            ],
        ],
        'fallback' => [
            400 => 'Chybové správy môžete nájsť v poli validationErrors.',
            403 => 'Skontrolujte váš autorizačný token a to, ku ktorým zdrojom sa pokúšate pristupovať.',
            401 => 'Skontrolujte autorizačné pole Http hlavičky.',
            404 => 'Skontrolujte url',
            405 => 'Http metóda, ktorú ste zvolili, nie je podporovaná týmto koncovým bodom.',
            500 => 'Nastala chyba na strane serveru.',
        ],
        'marked' => 'Kontaktujte prosím správcu a poskytnite mu tento hash: %mark% spolu s momentálnym časom, dátumom a url, na ktorú ste pristupovali, alebo skúste neskôr.',
    ],
];
