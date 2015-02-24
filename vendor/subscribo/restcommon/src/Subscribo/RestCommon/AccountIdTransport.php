<?php namespace Subscribo\RestCommon;

use Subscribo\RestCommon\Signature;

class AccountIdTransport
{
    /**
     * Sets account ID in Signature Options array
     * @param int|string $accountId integer or numeric string with integer value
     * @param array $options Other Signature options
     * @return array
     */
    public static function setAccountId($accountId, array $options = array())
    {
        $toAdd = ['accountId' => intval($accountId)];
        return Signature::addToDescription($toAdd, $options);
    }

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
