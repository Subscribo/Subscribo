<?php namespace Subscribo\RestCommon;

use Subscribo\RestCommon\Signature;

class AccountIdTransport
{
    /**
     * Get Account Id from Signature description
     *
     * @param array $description
     * @return int|null
     */
    public static function getAccountIdFromDescription(array $description)
    {
        if (empty($description['accountId'])) {
            return null;
        }
        return intval($description['accountId']);
    }

    /**
     * Get Account Id from result of calling of Signature::processIncomingRequest()
     *
     * @param array|null $processIncomingRequestResult
     * @return int|null
     */
    public static function extractAccountIdFromProcessIncomingRequestResult($processIncomingRequestResult)
    {
        $description = Signature::extractDescriptionFromProcessIncomingRequestResult($processIncomingRequestResult);
        return static::getAccountIdFromDescription($description);
    }
}
