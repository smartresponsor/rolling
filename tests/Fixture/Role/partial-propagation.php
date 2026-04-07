<?php

declare(strict_types=1);

return [
    'name' => 'partial-propagation',
    'engine' => 'rebac-minimal',
    'ns' => 'acme',
    'seed' => [
        ['objType' => 'team', 'objId' => 'eng', 'relation' => 'member', 'subjType' => 'user', 'subjId' => '77', 'subjRel' => null],
        ['objType' => 'repo', 'objId' => 'backend', 'relation' => 'reader', 'subjType' => 'team', 'subjId' => 'eng', 'subjRel' => 'member'],
    ],
    'checks' => [
        ['subject' => 'user:77', 'object' => 'repo:backend', 'relation' => 'reader', 'allow' => true],
        ['subject' => 'user:77', 'object' => 'repo:backend', 'relation' => 'writer', 'allow' => false],
    ],
    'scenarios' => [
        'propagation' => [
            'writes' => [
                ['objType' => 'repo', 'objId' => 'backend', 'relation' => 'writer', 'subjType' => 'team', 'subjId' => 'eng', 'subjRel' => 'member'],
            ],
            'checks' => [
                ['subject' => 'user:77', 'object' => 'repo:backend', 'relation' => 'reader', 'allow' => true],
                ['subject' => 'user:77', 'object' => 'repo:backend', 'relation' => 'writer', 'allow' => true],
            ],
        ],
    ],
    'note' => 'Verifies partial propagation expansion without regressing already granted access.',
];
