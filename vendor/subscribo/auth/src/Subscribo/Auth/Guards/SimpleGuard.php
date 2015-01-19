<?php namespace Subscribo\Auth\Guards;

use Subscribo\Auth\Interfaces\StatelessGuardInterface;
use Illuminate\Contracts\Auth\Guard;
use Subscribo\Auth\Traits\StatelessToNonStatelessTrait;
use Subscribo\Auth\Interfaces\StatelessAuthenticatableFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SimpleGuard extends BaseStatelessGuard implements StatelessGuardInterface, Guard {
    use StatelessToNonStatelessTrait;

    /**
     * @var Request
     */
    protected $request;

    public function __construct(StatelessAuthenticatableFactoryInterface $userFactory, Request $request = null)
    {
        $this->request = $request;
        parent::__construct($userFactory);
    }


    /**
     * @param string $field
     * @return null|Response
     */
    public function onceBasic($field = 'email')
    {
       $authHeaderField =  $this->request->headers->get('Authorization');
        if (empty($authHeaderField)) {
            return $this->assembleUnauthorizedResponse();
        }
        $parts = explode(' ', $authHeaderField);
        if (count($parts) < 2) {
            return $this->assembleUnauthorizedResponse();
        }
        if (strtolower($parts[0]) !== 'basic') {
            return $this->assembleUnauthorizedResponse();
        }
        $credentialString = base64_decode($parts[1], true);
        if (empty($credentialString)) {
            return $this->assembleUnauthorizedResponse();
        }
        $credentialArray = explode(':', $credentialString);
        if (count($credentialArray) < 2) {
            return $this->assembleUnauthorizedResponse();
        }
        $credentials = [
            $field => $credentialArray[0],
            'password' => $credentialArray[1],
        ];
        $valid = $this->once($credentials);
        if ($valid) {
            return null;
        }
        return $this->assembleUnauthorizedResponse();
    }

    protected function assembleUnauthorizedResponse($wwwAuthenticate = 'Basic', $body = 'Unauthorized')
    {
        $headers = ['WWW-Authenticate' => $wwwAuthenticate];
        $response = new Response($body, 401, $headers);
        return $response;
    }

}
