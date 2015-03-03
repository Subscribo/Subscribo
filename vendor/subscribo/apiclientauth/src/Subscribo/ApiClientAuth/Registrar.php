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
            'password' => 'required|confirmed|min:6'
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
    protected function assembleModel(array $data = null)
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
