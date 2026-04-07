<?php

declare(strict_types=1);

namespace App\Policy\Opa;

use App\Entity\Role\Scope;
use App\Entity\Role\PermissionKey;
use App\Entity\Role\SubjectId;

/**
 *
 */

/**
 *
 */
final class InputBuilder
{
    /**
     * @param \App\Entity\Role\SubjectId $s
     * @param \App\Entity\Role\PermissionKey $a
     * @param \App\Entity\Role\Scope $sc
     * @param array $context
     * @return array
     */
    public function build(SubjectId $s, PermissionKey $a, Scope $sc, array $context = []): array
    {
        return [
            'subject' => ['id' => $s->value()],
            'action' => $a->value(),
            'scope' => [
                'type' => $sc->type(),       // 'global' | 'tenant' | 'resource'
                'tenantId' => $sc->tenantId(), // may be null
                'resourceId' => $sc->resourceId(), // may be null
                'key' => $sc->key(),
            ],
            'context' => $context,
        ];
    }
}
