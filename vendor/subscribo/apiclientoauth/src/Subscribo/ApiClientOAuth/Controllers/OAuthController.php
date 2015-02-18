<?php namespace Subscribo\ApiClientOAuth\Controllers;

use Illuminate\Routing\Controller;
use Subscribo\ApiClientOAuth\Traits\OAuthLoginTrait;

/**
 * Class OAuthController
 *
 * @package Subscribo\ApiClientOAuth
 */
class OAuthController extends Controller
{
    use OAuthLoginTrait;
}
