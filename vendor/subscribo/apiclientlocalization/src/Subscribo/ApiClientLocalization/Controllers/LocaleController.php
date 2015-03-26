<?php namespace Subscribo\ApiClientLocalization\Controllers;

use Illuminate\Routing\Controller;
use Subscribo\ApiClientLocalization\Traits\LocaleAjaxTrait;
use Subscribo\ApiClientLocalization\Traits\LocaleRedirectBackTrait;

/**
 * Class LocaleController
 *
 * @package Subscribo\ApiClientLocalization
 */
class LocaleController extends Controller
{
    use LocaleAjaxTrait;
    use LocaleRedirectBackTrait;
}
