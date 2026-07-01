<?php

declare(strict_types=1);

namespace App\Rolling\Service\Rebac;

use App\Rolling\InfrastructureInterface\Rebac\TupleStoreInterface;

final class RebacRelationshipChecker
{
    public function __construct(private readonly TupleStoreInterface $store, private readonly int $maxDepth = 8)
    {
    }

    public function check(string $ns, string $subject, string $object, string $relation): array
    {
        // subject: "user:123" ; object: "doc:1" ; relation: "viewer"
        [$subjType, $subjId] = explode(':', $subject, 2);
        [$objType, $objId] = explode(':', $object, 2);
        $allow = $this->dfs($ns, $subjType, $subjId, $objType, $objId, $relation, 0);
        $rev = $this->store->currentToken();

        return ['allow' => $allow, 'reason' => $allow ? 'ok' : 'not_found', 'rev' => (string) $rev];
    }

    private function dfs(string $ns, string $subjType, string $subjId, string $objType, string $objId, string $relation, int $depth): bool
    {
        if ($depth > $this->maxDepth) {
            return false;
        }
        // direct tuples
        foreach ($this->store->readByObject($ns, $objType, $objId, $relation) as $t) {
            // direct match on subject
            if ($t->subjType === $subjType && $t->subjId === $subjId && null === $t->subjRel) {
                return true;
            }
            // indirect: subject reference "type:id#rel"
            if (null !== $t->subjRel) {
                // is subject a member of (type:id)#rel ?
                if ($this->dfs($ns, $subjType, $subjId, $t->subjType, $t->subjId, $t->subjRel, $depth + 1)) {
                    return true;
                }
            }
        }

        return false;
    }
}
