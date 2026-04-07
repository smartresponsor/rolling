<?php

declare(strict_types=1);

return [
    'name' => 'propagation-chain',
    'engine' => 'rebac-minimal',
    'ns' => 'acme',
    'seed' => [
        ['objType' => 'team', 'objId' => 'eng', 'relation' => 'member', 'subjType' => 'user', 'subjId' => '77', 'subjRel' => null],
    ],
    'checks' => [
        ['subject' => 'user:77', 'object' => 'repo:backend', 'relation' => 'reader', 'allow' => false],
    ],
    'scenarios' => [
        'propagation' => [
            'writes' => [
                ['objType' => 'repo', 'objId' => 'backend', 'relation' => 'reader', 'subjType' => 'team', 'subjId' => 'eng', 'subjRel' => 'member'],
            ],
            'checks' => [
                ['subject' => 'user:77', 'object' => 'repo:backend', 'relation' => 'reader', 'allow' => true],
            ],
        ],
    ],
];
