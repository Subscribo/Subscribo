<?php namespace Subscribo\ModelCore\Models;

use RuntimeException;
use Subscribo\ModelCore\Models\Service;
use Subscribo\ModelCore\Models\Customer;
use Subscribo\ModelCore\Models\Account;
use Subscribo\ModelCore\Models\ServicePool;

/**
 * Model CustomerRegistration
 *
 * Model class for being changed and used in the application
 */
class CustomerRegistration extends \Subscribo\ModelCore\Bases\CustomerRegistration
{
    const STATUS_PREPARED = 'prepared';
    const STATUS_MERGE_PROPOSED = 'merge_proposed';
    const STATUS_MERGE_CONFIRMED = 'merge_confirmed';
    const STATUS_MERGE_REJECTED = 'merge_rejected';
    const STATUS_NEW_ACCOUNT_GENERATED = 'new_account_generated';
    const STATUS_EXISTING_ACCOUNT_USED = 'existing_account_used';
    const STATUS_MERGED = 'merged';

    /**
     * @param Service|int|string $mergedToService
     * @param string $hash
     * @return $this
     * @throws \RuntimeException
     */
    public function markMergeProposed($mergedToService, $hash)
    {
        $mergedToServiceId = ($mergedToService instanceof Service) ? $mergedToService->id : $mergedToService;
        if ( ! ServicePool::servicesAreInSamePool($this->serviceId, $mergedToServiceId)) {
            throw new RuntimeException('Proposed service to merge is not in the same pool.');
        }
        $this->password = null;
        $this->mergedToServiceId = $mergedToServiceId;
        $this->hash = $hash;
        $this->status = static::STATUS_MERGE_PROPOSED;
        $this->save();
        return $this;
    }

    /**
     * @param Service|int|string $mergedToService
     * @param Customer|int|string $customer
     * @return $this
     * @throws \RuntimeException
     */
    public function markMergeConfirmed($mergedToService, $customer)
    {
        $mergedToServiceId = ($mergedToService instanceof Service) ? $mergedToService->id : $mergedToService;
        if (strval($mergedToServiceId) !== strval($this->mergedToServiceId)) {
            throw new RuntimeException('Provided service is different than proposed service.');
        }
        $customerId = ($customer instanceof Customer) ? $customer->id : $customer;
        $this->password = null;
        $this->customerId = $customerId;
        $this->status = static::STATUS_MERGE_CONFIRMED;
        $this->save();
        return $this;
    }

    /**
     * @param Service|int|string $mergedToService
     * @return $this
     * @throws \RuntimeException
     */
    public function markMergeRejected($mergedToService)
    {
        $mergedToServiceId = ($mergedToService instanceof Service) ? $mergedToService->id : $mergedToService;
        if (strval($mergedToServiceId) !== strval($this->mergedToServiceId)) {
            throw new RuntimeException('Provided service is different than proposed service.');
        }
        $this->password = null;
        $this->status = static::STATUS_MERGE_REJECTED;
        $this->save();
        return $this;
    }

    /**
     * @param Account|int|string $account
     * @param string $status
     * @return $this
     */
    public function finalize($account, $status)
    {
        $accountId = ($account instanceof Account) ? $account->id : $account;
        $this->password = null;
        $this->accountId = $accountId;
        $this->status = $status;
        $this->save();
        return $this;
    }

    /**
     * @return array
     */
    public function export()
    {
        return [
            'name' => $this->name,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'gender' => $this->gender,
            'street' => $this->street,
            'city' => $this->city,
            'post_code' => $this->postCode,
            'country' => $this->country,
            'phone' => $this->phone,
            'email' => $this->email,
        ];
    }
}
