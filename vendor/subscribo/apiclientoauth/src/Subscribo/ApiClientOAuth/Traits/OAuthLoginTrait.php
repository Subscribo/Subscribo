<?php namespace Subscribo\ApiClientOAuth\Traits;

use Subscribo\ApiClientOAuth\OAuthManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class OAuthLoginTrait
 *
 * @package Subscribo\ApiClientOAuth
 */
trait OAuthLoginTrait
{
    public function getLogin(OAuthManager $manager, $driver)
    {
        if (false === array_search($driver, $manager->getAvailableDrivers(), true)) {
            throw new NotFoundHttpException();
        }
        return $manager->assembleRedirect($driver);
    }

    public function getHandle(OAuthManager $manager, $driver)
    {
        if (false === array_search($driver, $manager->getAvailableDrivers(), true)) {
            return "WRONG";
            throw new NotFoundHttpException();
        }
        $user = $manager->getUser($driver);
        dd($user);



    }

}