<?php

declare(strict_types=1);

return [
    'name' => 'revoke-after-propagation',
    'engine' => 'rebac-minimal',
    'ns' => 'acme',
    'seed' => [
        ['objType' => 'team', 'objId' => 'ops', 'relation' => 'member', 'subjType' => 'user', 'subjId' => '88', 'subjRel' => null],
        ['objType' => 'repo', 'objId' => 'infra', 'relation' => 'reader', 'subjType' => 'team', 'subjId' => 'ops', 'subjRel' => 'member'],
    ],
    'checks' => [
        ['subject' => 'user:88', 'object' => 'repo:infra', 'relation' => 'reader', 'allow' => true],
    ],
    'scenarios' => [
        'elimination' => [
            'deletes' => [
                ['objType' => 'repo', 'objId' => 'infra', 'relation' => 'reader', 'subjType' => 'team', 'subjId' => 'ops', 'subjRel' => 'member'],
            ],
            'checks' => [
                ['subject' => 'user:88', 'object' => 'repo:infra', 'relation' => 'reader', 'allow' => false],
            ],
        ],
    ],
    'note' => 'Verifies revoke after propagation removes effective access.',
];
