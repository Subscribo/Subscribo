<?php namespace Subscribo\ModelCore\Models;

use InvalidArgumentException;
use Subscribo\ModelCore\Models\Customer;
use Subscribo\ModelCore\Models\Service;

/**
 * Model Account
 *
 * Model class for being changed and used in the application
 */
class Account extends \Subscribo\ModelCore\Bases\Account
{
    /**
     * @param int|Customer $customer
     * @param int|Service $service
     * @param Locale|null $locale
     * @return Account|static
     * @throws \InvalidArgumentException
     */
    public static function generate($customer, $service, Locale $locale = null)
    {
        //We reload customer from DB to get default value for rememberLocale
        $customerId = ($customer instanceof Customer) ? $customer->id : $customer;
        /** @var Customer $customer */
        $customer = Customer::find($customerId);
        if (empty($customer)) {
            throw new InvalidArgumentException('Account::generate(): provided customer was neither of Customer type nor found');
        }
        if ( ! ($service instanceof Service)) {
            Service::find($service);
        }
        if (empty($service)) {
            throw new InvalidArgumentException('Account::generate(): provided service was neither of Service type nor found');
        }
        $locale = $locale ?: $service->defaultLocale;
        /** @var Account $account */
        $account = new static();
        $account->customerId = $customerId;
        $account->serviceId = $service->id;
        $account->locale = $locale->identifier;
        $account->rememberLocale = $service->calculateRememberLocale($customer->rememberLocale);
        $account->save();
        return $account;
    }

    /**
     * @param string $rememberToken
     * @param int|string $id
     * @param int|string $serviceId
     * @return Account|null
     */
    public static function findRemembered($rememberToken, $id, $serviceId)
    {
        $query = static::query();
        $query->where('id', $id)
            ->where('remember_token', $rememberToken)
            ->where('service_id', $serviceId);
        return $query->first();
    }

    /**
     * @param string $email
     * @param int|string $serviceId
     * @return Account|null
     */
    public static function findByEmailAndServiceId($email, $serviceId)
    {
        $serviceId = strval($serviceId);
        $query = Customer::query()->where('email', $email)->with('accounts');
        $customers = $query->get();
        /** @var Customer $customer */
        foreach ($customers as $customer) {
            foreach($customer->accounts as $account) {
                if (strval($account->serviceId) === $serviceId) {
                    $account->setRelation('customer', $customer);
                    return $account;
                }
            }
        }
        return null;
    }

}
