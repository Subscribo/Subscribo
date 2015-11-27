<?php namespace Subscribo\RestCommon;

class RestCommon {

    const ACCESS_TOKEN_HEADER_FIELD_NAME = 'Subscribo-Access-Token';

    const OAUTH_PROVIDER_NAME_FOR_SUBSCRIBO_THICK_CLIENT = 'client';

    public static $responseContentItemsToRemove = [
        400 => [],
        404 => [],
        'anyStatus' => [
            'metaData',
            'developmentData',
        ],
    ];

}
