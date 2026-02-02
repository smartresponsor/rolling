<?php
declare(strict_types=1);

namespace Policy\Role\Opa;

use src\Entity\Role\{Scope};
use src\Entity\Role\PermissionKey;
use src\Entity\Role\SubjectId;

/**
 *
 */

/**
 *
 */
final class InputBuilder
{
    /**
     * @param \src\Entity\Role\SubjectId $s
     * @param \src\Entity\Role\PermissionKey $a
     * @param \src\Entity\Role\Scope $sc
     * @param array $context
     * @return array
     */
    public function build(SubjectId $s, PermissionKey $a, Scope $sc, array $context = []): array
    {
        return [
            'subject' => ['id' => (string)$s],
            'action' => (string)$a,
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
