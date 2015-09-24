<?php

return array(
    'protocol'  => env('SUBSCRIBO_REST_CLIENT_PROTOCOL', 'https'),

    'host'      => env('SUBSCRIBO_REST_CLIENT_HOST', 'subscribo.io'),

    'uri_base'  => env('SUBSCRIBO_REST_CLIENT_URI_BASE', '/api/v1'),

    'token_ring' => env('SUBSCRIBO_REST_CLIENT_TOKEN_RING', null),

    'port'      => env('SUBSCRIBO_REST_CLIENT_PORT', null),
);
