<?php namespace Subscribo\ModelCore\Models;

use InvalidArgumentException;
use Subscribo\ModelCore\Models\Customer;
use Subscribo\ModelCore\Models\Service;
use Subscribo\ModelBase\Traits\HasHashTrait;

/**
 * Model Account
 *
 * Model class for being changed and used in the application
 */
class Account extends \Subscribo\ModelCore\Bases\Account
{
    use HasHashTrait;

    /**
     * @param string $accountAccessToken
     * @return Account|null
     */
    public static function findByAccountAccessToken($accountAccessToken)
    {
        if (empty($accountAccessToken)) {

            return null;
        }

        return static::findByHash($accountAccessToken);
    }

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
        $account = static::makeWithHash();
        $account->customerId = $customerId;
        $account->serviceId = $service->id;
        $account->locale = $locale->identifier;
        $account->rememberLocale = $service->calculateRememberLocale($customer->rememberLocale);
        $account->save();
        return $account;
    }

    /**
     * @param string $rememberToken
     * @param string $accountAccessToken
     * @param int|string $serviceId
     * @return Account|null
     */
    public static function findRemembered($rememberToken, $accountAccessToken, $serviceId)
    {
        $query = static::query();
        $query->where('access_token', $accountAccessToken)
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

    /**
     * @param bool $withAccessToken
     * @return array
     */
    public function export($withAccessToken = false)
    {
        $result = $this->toArray();
        if ($withAccessToken) {
            $result['access_token'] = $this->accessToken;
        }

        return $result;
    }

    /**
     * @return string
     */
    protected static function getDefaultHashColumnName()
    {
        return 'access_token';
    }
}
