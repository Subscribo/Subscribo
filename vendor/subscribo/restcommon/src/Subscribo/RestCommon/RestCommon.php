<?php namespace Subscribo\RestCommon;

class RestCommon {

    const ACCESS_TOKEN_HEADER_FIELD_NAME = 'Subscribo-Access-Token';

    public static $responseContentItemsToRemove = [
        400 => [],
        404 => [],
        'anyStatus' => [
            'metaData',
            'developmentData',
        ],
    ];

}
