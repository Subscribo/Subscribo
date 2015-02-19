<?php namespace Subscribo\Api1\Controllers;

use Subscribo\Api1\AbstractController;

class OAuthController extends AbstractController
{
    protected static $controllerUriStub = 'oauth';

    public function actionGetConfig($driver)
    {
        $result = [
            'result' => [
                $driver => [
                    'config' => [
                        'client_id' => '406816512743553',
                        'client_secret' => '66cefb3c643ce93d327491cdc351ed13',
                        'redirect' => 'http://frontend.sio.kochabo.at/oauth/handle/facebook'


                    ],
                    'scopes' => [
                        'email', //'user_about_me', 'user_friends'

                    ],
                ],
            ],
        ];
        return $result;
    }
}