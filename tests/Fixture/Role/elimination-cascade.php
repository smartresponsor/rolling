<?php

declare(strict_types=1);

return [
    'name' => 'elimination-cascade',
    'engine' => 'rebac-minimal',
    'ns' => 'acme',
    'seed' => [
        ['objType' => 'group', 'objId' => 'ops', 'relation' => 'member', 'subjType' => 'user', 'subjId' => '99', 'subjRel' => null],
        ['objType' => 'cluster', 'objId' => 'prod', 'relation' => 'viewer', 'subjType' => 'group', 'subjId' => 'ops', 'subjRel' => 'member'],
    ],
    'checks' => [
        ['subject' => 'user:99', 'object' => 'cluster:prod', 'relation' => 'viewer', 'allow' => true],
    ],
    'scenarios' => [
        'elimination' => [
            'deletes' => [
                ['objType' => 'cluster', 'objId' => 'prod', 'relation' => 'viewer', 'subjType' => 'group', 'subjId' => 'ops', 'subjRel' => 'member'],
            ],
            'checks' => [
                ['subject' => 'user:99', 'object' => 'cluster:prod', 'relation' => 'viewer', 'allow' => false],
            ],
        ],
    ],
    'note' => 'Removes propagation edge and verifies access elimination.',
];
