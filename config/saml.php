<?php

return [

    'strict' => true,
    'debug' => true,

    'sp' => [
        'entityId' => env('SAML_URL') . '/saml/metadata',
        'assertionConsumerService' => [
            'url' => env('SAML_URL') . '/saml',
        ],
        'singleLogoutService' => [
            'url' => env('SAML_URL') . '/saml/logout',
        ],
        'x509cert' => '',
        'privateKey' => '',
    ],

    'idp' => [
        'entityId' => 'https://tdi.customerfi.com/cloudpass/metadata',
        'singleSignOnService' => [
            'url' => 'https://tdi.customerfi.com/cloudpass/launchpad/launchApp/99e9d94a0f914ae0974f817277fac0f5/9lndgj53tx3zysjx1410282663331',
        ],
        'singleLogoutService' => [
            'url' => 'https://tdi.customerfi.com/cloudpass/login/logout?forward=https://tdi.customerfi.com/cloudpass/launchpad/launchApp/99e9d94a0f914ae0974f817277fac0f5/9lndgj53tx3zysjx1410282663331',
        ],
        'x509cert' => file_get_contents(storage_path('saml/tdi.customerfi.com.crt')),
    ],
];
