<?php

declare(strict_types=1);

namespace App\Rolling\Policy\Opa;

use App\Rolling\Entity\Role\PermissionKey;
use App\Rolling\Entity\Role\Scope;
use App\Rolling\Entity\Role\SubjectId;

final class InputBuilder
{
    /**
     * @param SubjectId     $s
     * @param PermissionKey $a
     * @param Scope         $sc
     * @param array         $context
     *
     * @return array
     */
    public function build(SubjectId $s, PermissionKey $a, Scope $sc, array $context = []): array
    {
        return [
            'subject' => ['id' => $s->value()],
            'action' => $a->value(),
            'scope' => [
                'type' => $sc->type(),
                'tenantId' => $sc->tenantId(),
                'resourceId' => $sc->resourceId(),
                'key' => $sc->key(),
            ],
            'context' => $context,
        ];
    }
}
