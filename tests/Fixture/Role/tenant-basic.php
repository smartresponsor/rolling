<?php

declare(strict_types=1);

return [
    'name' => 'tenant-basic',
    'engine' => 'rebac-minimal',
    'ns' => 'acme',
    'seed' => [
        ['objType' => 'group', 'objId' => 'dev', 'relation' => 'member', 'subjType' => 'user', 'subjId' => '42', 'subjRel' => null],
        ['objType' => 'doc', 'objId' => '1', 'relation' => 'viewer', 'subjType' => 'group', 'subjId' => 'dev', 'subjRel' => 'member'],
    ],
    'checks' => [
        ['subject' => 'user:42', 'object' => 'doc:1', 'relation' => 'viewer', 'allow' => true],
        ['subject' => 'user:41', 'object' => 'doc:1', 'relation' => 'viewer', 'allow' => false],
    ],
];
