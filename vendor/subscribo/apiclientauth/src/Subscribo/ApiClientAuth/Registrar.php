<?php namespace Subscribo\ApiClientAuth;

use Subscribo\ApiClientAuth\Connectors\AccountConnector;

class Registrar
{

    protected $accountConnector;

    protected $model;

    public function __construct(AccountConnector $accountConnector,  $model = '\\Subscribo\\ApiClientAuth\\Account')
    {
        $this->accountConnector = $accountConnector;
        $this->model = $model;
    }

    public static function getValidationRules()
    {
        return static::getRegistrationValidationRules() + static::getAddressValidationRules();
    }

    public static function getRegistrationValidationRules()
    {
        return [
            'name'  => 'max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|confirmed|min:6',
            'remember_me' => 'boolean',
        ];
    }

    public static function getAddressValidationRules()
    {
        return [
            'gender' => 'in:man,woman',
            'first_name' => 'max:100',
            'last_name' => 'required_with:city|max:100',
            'street' => 'required_with:city|max:255',
            'post_code' => 'max:30',
            'city' => 'max:100',
            'country' => 'required_with:city|max:100',
            'delivery_information' => 'max:500',
            'phone' => 'max:30',
            'mobile' => 'max:30',
        ];
    }


    /**
     * @param array $data
     * @return \Illuminate\Contracts\Auth\Authenticatable|Account|null
     */
    public function attempt(array $data)
    {
        $response = $this->accountConnector->postRegistration($data);

        return $this->assembleModel($response);
    }

    /**
     * @param array $data
     * @return \Illuminate\Contracts\Auth\Authenticatable|Account|null
     */
    public function resumeAttempt(array $data)
    {
        return $this->makeAuthenticatableModelFromRawRegistrationResponse($data);
    }


    public function getRawRegistrationResponse(array $data)
    {
        return $this->accountConnector->postRegistrationRaw($data);
    }

    /**
     * @param array $responseData
     * @return \Illuminate\Contracts\Auth\Authenticatable|null|Account
     */
    public function makeAuthenticatableModelFromRawRegistrationResponse(array $responseData)
    {
        $response = $this->accountConnector->processPostRegistrationRawResponse($responseData);

        return $this->assembleModel($response);
    }

    /**
     * @param array $data
     * @return null|\Subscribo\ApiClientAuth\Account|\Illuminate\Contracts\Auth\Authenticatable
     */
    public function assembleModel(array $data = null)
    {
        if (empty($data)) {
            return null;
        }
        return new $this->model($data);
    }

    public function isModel($entity)
    {
        return ($entity instanceof $this->model);
    }
}
