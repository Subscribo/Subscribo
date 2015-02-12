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
     * @return null|QuestionList|mixed
     */
    public function attempt(array $data)
    {
        $response = $this->accountConnector->postRegistration($data);
        if (is_array($response)) {
            return new $this->model($response);
        }
        return $response;
    }

    public function isModel($entity)
    {
        return ($entity instanceof $this->model);
    }
}
