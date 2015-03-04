<?php namespace Subscribo\Api1\Controllers;

use Subscribo\Api1\AbstractController;
use Subscribo\OAuthCommon\AbstractOAuthManager;
use Subscribo\Exception\Exceptions\NotFoundHttpException;
use Subscribo\Exception\Exceptions\InstanceNotFoundHttpException;
use Subscribo\ModelCore\Models\OAuthConfiguration;

class OAuthController extends AbstractController
{
    protected static $controllerUriStub = 'oauth';

    public function actionGetConfig($provider)
    {
        $validated = $this->validateRequestQuery(['redirect' => 'url']);
        if (false === array_search($provider, AbstractOAuthManager::getAvailableDrivers(), true)) {
            throw new NotFoundHttpException();
        }
        $configuration = OAuthConfiguration::findByProviderAndServiceId($provider, $this->context->getServiceId());
        if (empty($configuration)) {
            throw new InstanceNotFoundHttpException();
        }
        $redirect = $configuration->redirect ?: array_get($validated, 'redirect');
        $redirect = trim($redirect) ?: $this->assembleRedirect($provider);
        $scopes = $configuration->scopes ? ((array) json_decode($configuration->scopes)) : null;
        $result = [
            'result' => [
                $provider => [
                    'config' => [
                        'provider'  => $provider,
                        'client_id' => $configuration->identifier,
                        'client_secret' => $configuration->secret,
                        'redirect' => $redirect,
                    ],
                    'scopes' => $scopes,
                ],
            ],
        ];
        return $result;
    }


    /**
     * Fallback assembling of redirect
     * @param $provider
     * @return null|string
     */
    protected function assembleRedirect($provider)
    {
        $service = $this->context->getService();
        $url = $service ? (rtrim($service->url, '/').'/oauth/handle/'.$provider) : null;
        return $url;
    }
}
