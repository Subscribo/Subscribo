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

    public function getValidationRules()
    {
        return [
            'name'  => 'max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|confirmed|min:6',
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
     * @return null|Account
     */
    public function attempt(array $data)
    {
        $response = $this->accountConnector->postRegistration($data);
        return $this->assembleModel($response);
    }

    /**
     * @param array $data
     * @return null|Account
     */
    public function resumeAttempt(array $data)
    {
        $response = $this->accountConnector->resumePostRegistration($data);
        return $this->assembleModel($response);
    }

    /**
     * @param array $data
     * @return null|\Subscribo\ApiClientAuth\Account
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
