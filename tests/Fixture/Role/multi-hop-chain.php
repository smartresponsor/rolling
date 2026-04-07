<?php

declare(strict_types=1);

return [
    'name' => 'multi-hop-chain',
    'engine' => 'rebac-minimal',
    'ns' => 'acme',
    'seed' => [
        ['objType' => 'org', 'objId' => 'platform', 'relation' => 'member', 'subjType' => 'user', 'subjId' => '55', 'subjRel' => null],
        ['objType' => 'team', 'objId' => 'core', 'relation' => 'member', 'subjType' => 'org', 'subjId' => 'platform', 'subjRel' => 'member'],
    ],
    'checks' => [
        ['subject' => 'user:55', 'object' => 'repo:billing', 'relation' => 'reader', 'allow' => false],
    ],
    'scenarios' => [
        'propagation' => [
            'writes' => [
                ['objType' => 'repo', 'objId' => 'billing', 'relation' => 'reader', 'subjType' => 'team', 'subjId' => 'core', 'subjRel' => 'member'],
            ],
            'checks' => [
                ['subject' => 'user:55', 'object' => 'repo:billing', 'relation' => 'reader', 'allow' => true],
            ],
        ],
    ],
    'note' => 'Verifies multi-hop membership propagation through org -> team -> repo.',
];
