<?php

return [

    'strict' => true,
    'debug' => true,

    'sp' => [
        'entityId' => 'https://totvs.bw8.tech' . '/saml/metadata',
        'assertionConsumerService' => [
            'url' => 'https://totvs.bw8.tech' . '/saml',
        ],
        'singleLogoutService' => [
            'url' => 'https://totvs.bw8.tech' . '/saml/logout',
        ],
        'x509cert' => file_get_contents(storage_path('saml/sp.crt')),
        'privateKey' => file_get_contents(storage_path('saml/sp.key')),
    ],

    'idp' => [
        'entityId' => 'TotvsLabs',
        'singleSignOnService' => [
            'url' => 'https://tdi.customerfi.com/cloudpass/SPInitPost/receiveSSORequest/9lndgj53tx3zysjx1410282663331/99e9d94a0f914ae0974f817277fac0f5',
        ],
        'singleLogoutService' => [
            'url' => 'https://tdi.customerfi.com/cloudpass/login/logout?forward=https://tdi.customerfi.com/cloudpass/launchpad/launchApp/99e9d94a0f914ae0974f817277fac0f5/9lndgj53tx3zysjx1410282663331',
        ],
        'x509cert' => file_get_contents(storage_path('saml/tdi.customerfi.com.crt')),
    ],
];
