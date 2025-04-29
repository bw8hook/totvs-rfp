<?php

return [

    'strict' => true,
    'debug' => true,

    'sp' => [
        'entityId' => 'https://totvs.bw8.tech' . '/saml/metadata',
        'assertionConsumerService' => [
            'url' => 'https://totvs.bw8.tech' . '/saml',
            'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST'
        ],
        'singleLogoutService' => [
            'url' => 'https://totvs.bw8.tech' . '/saml/logout',
            'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect'
        ],
        'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
        'x509cert' => file_get_contents(storage_path('saml/sp.crt')),
        'privateKey' => file_get_contents(storage_path('saml/sp.key')),
    ],

    'idp' => [
        'entityId' => 'TotvsLabs',
        'singleSignOnService' => [
            'url' => 'https://tdi.customerfi.com/cloudpass/SPInitPost/receiveSSORequest/9lndgj53tx3zysjx1410282663331/99e9d94a0f914ae0974f817277fac0f5',
            'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST'
        ],
        'singleLogoutService' => [
            'url' => 'https://tdi.customerfi.com/cloudpass/login/logout?forward=https://tdi.customerfi.com/cloudpass/launchpad/launchApp/99e9d94a0f914ae0974f817277fac0f5/9lndgj53tx3zysjx1410282663331',
            'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect'
        ],
        'x509cert' => file_get_contents(storage_path('saml/tdi.customerfi.com.crt')),
    ],
    'security' => [
        'nameIdEncrypted' => false,
        'authnRequestsSigned' => false,
        'logoutRequestSigned' => false,
        'logoutResponseSigned' => false,
        'signMetadata' => false,
        'wantMessagesSigned' => false,
        'wantAssertionsSigned' => true,
        'wantNameIdEncrypted' => false,
        'requestedAuthnContext' => true,
        'signatureAlgorithm' => 'http://www.w3.org/2000/09/xmldsig#rsa-sha1',
        'digestAlgorithm' => 'http://www.w3.org/2000/09/xmldsig#sha1',
    ]
];
