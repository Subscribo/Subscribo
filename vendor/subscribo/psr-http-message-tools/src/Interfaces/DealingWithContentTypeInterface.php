<?php namespace Subscribo\PsrHttpMessageTools\Interfaces;

/**
 * Class DealingWithContentTypeInterface
 *
 * @package Subscribo\PsrHttpMessageTools
 */
interface DealingWithContentTypeInterface
{
    const CONTENT_TYPE_FORM = 'application/x-www-form-urlencoded';
    const CONTENT_TYPE_JSON = 'application/json';
    const CONTENT_TYPE_XML = 'application/xml';

    const DEFAULT_CONTENT_TYPE = self::CONTENT_TYPE_FORM;
}
