<?php

declare(strict_types=1);

return [
    'name' => 'relation-override',
    'engine' => 'rebac-minimal',
    'ns' => 'acme',
    'seed' => [
        ['objType' => 'team', 'objId' => 'platform', 'relation' => 'member', 'subjType' => 'user', 'subjId' => '21', 'subjRel' => null],
        ['objType' => 'repo', 'objId' => 'payments', 'relation' => 'reader', 'subjType' => 'team', 'subjId' => 'platform', 'subjRel' => 'member'],
    ],
    'checks' => [
        ['subject' => 'user:21', 'object' => 'repo:payments', 'relation' => 'reader', 'allow' => true, 'label' => 'reader-allow'],
        ['subject' => 'user:21', 'object' => 'repo:payments', 'relation' => 'writer', 'allow' => false, 'label' => 'writer-missing'],
    ],
    'scenarios' => [
        'propagation' => [
            'writes' => [
                ['objType' => 'repo', 'objId' => 'payments', 'relation' => 'writer', 'subjType' => 'team', 'subjId' => 'platform', 'subjRel' => 'member'],
            ],
            'checks' => [
                ['subject' => 'user:21', 'object' => 'repo:payments', 'relation' => 'reader', 'allow' => true, 'label' => 'reader-preserved'],
                ['subject' => 'user:21', 'object' => 'repo:payments', 'relation' => 'writer', 'allow' => true, 'label' => 'writer-added'],
            ],
        ],
    ],
    'note' => 'Exercises relation override semantics by adding a stronger relation without regressing the original one.',
];
