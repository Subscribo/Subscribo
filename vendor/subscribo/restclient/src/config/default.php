<?php

return array(
    'protocol'  => env('SUBSCRIBO_REST_CLIENT_PROTOCOL', 'http'),

    'host'      => env('SUBSCRIBO_REST_CLIENT_HOST', 'localhost'),

    'uri_base'  => env('SUBSCRIBO_REST_CLIENT_URI_BASE', '/api/v1'),

    'token_ring' => env('SUBSCRIBO_REST_CLIENT_TOKEN_RING', null),

);
