<?php

namespace Subscribo\RestCommon;

use Subscribo\RestCommon\Signature;

/**
 * Class AccountAccessTokenTransport
 *
 * @package Subscribo\RestCommon
 */
class AccountAccessTokenTransport
{
    const ACCOUNT_ACCESS_TOKEN_DESCRIPTION_FIELD = 'accountAccessToken';
    /**
     * Get Account Access Token from Signature description
     *
     * @param array $description
     * @return string|null
     */
    public static function getAccountAccessTokenFromDescription(array $description)
    {
        if (empty($description[self::ACCOUNT_ACCESS_TOKEN_DESCRIPTION_FIELD])) {

            return null;
        }

        return strval($description[self::ACCOUNT_ACCESS_TOKEN_DESCRIPTION_FIELD]);
    }

    /**
     * Get Account Access Token from result of calling of Signature::processIncomingRequest()
     *
     * @param array|null $processIncomingRequestResult
     * @return string|null
     */
    public static function extractAccountAccessTokenFromProcessIncomingRequestResult($processIncomingRequestResult)
    {
        $description = Signature::extractDescriptionFromProcessIncomingRequestResult($processIncomingRequestResult);

        return static::getAccountAccessTokenFromDescription($description);
    }
}
