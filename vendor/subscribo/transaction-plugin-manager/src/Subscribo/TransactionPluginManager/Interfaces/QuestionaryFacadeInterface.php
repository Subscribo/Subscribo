<?php

namespace Subscribo\TransactionPluginManager\Interfaces;

use Subscribo\RestCommon\Questionary;

/**
 * Interface QuestionaryFacadeInterface
 *
 * @package Subscribo\TransactionPluginManager
 */
interface QuestionaryFacadeInterface
{
    const CODE_GENERIC_QUESTIONARY                      = Questionary::CODE_GENERIC_QUESTIONARY;
    const CODE_MULTIPLE_QUESTIONARY                     = Questionary::CODE_MULTIPLE_QUESTIONARY;
    const CODE_DATE                                     = Questionary::CODE_DATE;
    const CODE_CUSTOMER_BIRTH_DATE                      = Questionary::CODE_CUSTOMER_BIRTH_DATE;
    const CODE_CUSTOMER_NATIONAL_IDENTIFICATION_NUMBER  = Questionary::CODE_CUSTOMER_NATIONAL_IDENTIFICATION_NUMBER;
    const CODE_CUSTOMER_GENDER                          = Questionary::CODE_CUSTOMER_GENDER;

    /**
     * @return Questionary
     */
    public function getQuestionaryInstance();
}
