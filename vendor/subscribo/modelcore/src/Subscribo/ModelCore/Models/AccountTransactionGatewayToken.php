<?php

namespace Subscribo\ModelCore\Models;

use Subscribo\ModelCore\Models\Account;
use Subscribo\ModelCore\Models\TransactionGatewayConfiguration;


/**
 * Model AccountTransactionGatewayToken
 *
 * Model class for being changed and used in the application
 */
class AccountTransactionGatewayToken extends \Subscribo\ModelCore\Bases\AccountTransactionGatewayToken
{
    /**
     * @param TransactionGatewayConfiguration $transactionGatewayConfiguration
     * @param Account|int $account
     * @param $token
     * @param bool $allowMultiple
     * @return AccountTransactionGatewayToken
     */
    public static function addToken(TransactionGatewayConfiguration $transactionGatewayConfiguration, $account, $token, $allowMultiple = true)
    {
        $accountId = ($account instanceof Account) ? $account->id : $account;
        $rootTransactionGatewayConfigurationId = $transactionGatewayConfiguration->acquireRoot()->id;

        if ( ! $allowMultiple) {
            $query = static::query()->where('account_id', $accountId)
                ->where('transaction_gateway_configuration_id', $rootTransactionGatewayConfigurationId);
            /** @var AccountTransactionGatewayToken $found */
            $found = $query->first();
            if ($found) {
                $found->token = $token;
                $found->save();

                return $found;
            }
        }
        $instance = new static();
        $instance->accountId = $accountId;
        $instance->transactionGatewayConfigurationId = $rootTransactionGatewayConfigurationId;
        $instance->token = $token;
        $instance->save();

        return $instance;
    }

    /**
     * @param TransactionGatewayConfiguration $transactionGatewayConfiguration
     * @param Account|int $account
     * @return AccountTransactionGatewayToken
     */
    public static function findDefaultOrLast(TransactionGatewayConfiguration $transactionGatewayConfiguration, $account)
    {
        $accountId = ($account instanceof Account) ? $account->id : $account;
        $rootTransactionGatewayConfigurationId = $transactionGatewayConfiguration->acquireRoot()->id;

        $queryDefault = static::query()->where('account_id', $accountId)
            ->where('transaction_gateway_configuration_id', $rootTransactionGatewayConfigurationId)
            ->where('is_default', true)
            ->orderBy('id', 'desc');

        $defaultInstance = $queryDefault->first();
        if ($defaultInstance) {

            return $defaultInstance;
        }
        $queryLast = static::query()->where('account_id', $accountId)
            ->where('transaction_gateway_configuration_id', $rootTransactionGatewayConfigurationId)
            ->orderBy('id', 'desc');

        return $queryLast->first();
    }
}
