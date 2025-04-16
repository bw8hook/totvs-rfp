<?php

return [

    'strict' => true,
    'debug' => true,

    'sp' => [
        'entityId' => env('APP_URL') . '/saml/metadata',
        'assertionConsumerService' => [
            'url' => env('APP_URL') . '/saml',
        ],
        'singleLogoutService' => [
            'url' => env('APP_URL') . '/saml/logout',
        ],
        'x509cert' => '',
        'privateKey' => '',
    ],

    'idp' => [
        'entityId' => 'https://tdi.customerfi.com/cloudpass/metadata',
        'singleSignOnService' => [
            'url' => 'https://tdi.customerfi.com/cloudpass/IDPInitSSO/receiveSSORequest',
        ],
        'singleLogoutService' => [
            'url' => '', // opcional
        ],
        'x509cert' => file_get_contents(storage_path('saml/tdi.customerfi.com.crt')),
    ],
];
