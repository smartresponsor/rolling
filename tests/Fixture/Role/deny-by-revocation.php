<?php

declare(strict_types=1);

return [
    'name' => 'deny-by-revocation',
    'engine' => 'rebac-minimal',
    'ns' => 'acme',
    'seed' => [
        ['objType' => 'team', 'objId' => 'security', 'relation' => 'member', 'subjType' => 'user', 'subjId' => '66', 'subjRel' => null],
        ['objType' => 'repo', 'objId' => 'vault', 'relation' => 'reader', 'subjType' => 'team', 'subjId' => 'security', 'subjRel' => 'member'],
    ],
    'checks' => [
        ['subject' => 'user:66', 'object' => 'repo:vault', 'relation' => 'reader', 'allow' => true, 'label' => 'baseline-allow'],
    ],
    'scenarios' => [
        'elimination' => [
            'deletes' => [
                ['objType' => 'repo', 'objId' => 'vault', 'relation' => 'reader', 'subjType' => 'team', 'subjId' => 'security', 'subjRel' => 'member'],
            ],
            'checks' => [
                ['subject' => 'user:66', 'object' => 'repo:vault', 'relation' => 'reader', 'allow' => false, 'label' => 'revoked-means-denied'],
            ],
        ],
    ],
    'note' => 'Rebac-minimal has no explicit deny tuple, so revocation is the supported elimination path to an effective deny state.',
];
