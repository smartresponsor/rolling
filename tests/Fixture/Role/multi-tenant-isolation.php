<?php

declare(strict_types=1);

return [
    'name' => 'multi-tenant-isolation',
    'engine' => 'rebac-minimal',
    'ns' => 'tenant-a',
    'seed' => [
        ['ns' => 'tenant-a', 'objType' => 'team', 'objId' => 'eng', 'relation' => 'member', 'subjType' => 'user', 'subjId' => '42', 'subjRel' => null],
        ['ns' => 'tenant-a', 'objType' => 'repo', 'objId' => 'billing', 'relation' => 'reader', 'subjType' => 'team', 'subjId' => 'eng', 'subjRel' => 'member'],
        ['ns' => 'tenant-b', 'objType' => 'team', 'objId' => 'eng', 'relation' => 'member', 'subjType' => 'user', 'subjId' => '42', 'subjRel' => null],
    ],
    'checks' => [
        ['ns' => 'tenant-a', 'subject' => 'user:42', 'object' => 'repo:billing', 'relation' => 'reader', 'allow' => true, 'label' => 'tenant-a-allow'],
        ['ns' => 'tenant-b', 'subject' => 'user:42', 'object' => 'repo:billing', 'relation' => 'reader', 'allow' => false, 'label' => 'tenant-b-isolated'],
    ],
    'scenarios' => [
        'propagation' => [
            'writes' => [
                ['ns' => 'tenant-b', 'objType' => 'repo', 'objId' => 'billing', 'relation' => 'reader', 'subjType' => 'team', 'subjId' => 'eng', 'subjRel' => 'member'],
            ],
            'checks' => [
                ['ns' => 'tenant-a', 'subject' => 'user:42', 'object' => 'repo:billing', 'relation' => 'reader', 'allow' => true, 'label' => 'tenant-a-still-allow'],
                ['ns' => 'tenant-b', 'subject' => 'user:42', 'object' => 'repo:billing', 'relation' => 'reader', 'allow' => true, 'label' => 'tenant-b-local-allow'],
            ],
        ],
    ],
    'note' => 'Verifies same identities remain isolated across namespaces until local tenant tuples are written.',
];
