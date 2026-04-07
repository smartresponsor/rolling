<?php
declare(strict_types=1);

namespace App\Service\Rebac;

use App\InfrastructureInterface\Rebac\TupleStoreInterface;

/**
 *
 */

/**
 *
 */
final class Checker
{
    /**
     * @param \App\InfrastructureInterface\Rebac\TupleStoreInterface $store
     * @param int $maxDepth
     */
    public function __construct(private readonly TupleStoreInterface $store, private readonly int $maxDepth = 8)
    {
    }

    /**
     * @param string $ns
     * @param string $subject
     * @param string $object
     * @param string $relation
     * @return array
     */
    public function check(string $ns, string $subject, string $object, string $relation): array
    {
        // subject: "user:123" ; object: "doc:1" ; relation: "viewer"
        [$subjType, $subjId] = explode(':', $subject, 2);
        [$objType, $objId] = explode(':', $object, 2);
        $allow = $this->dfs($ns, $subjType, $subjId, $objType, $objId, $relation, 0);
        $rev = $this->store->currentToken();
        return ['allow' => $allow, 'reason' => $allow ? 'ok' : 'not_found', 'rev' => (string)$rev];
    }

    /**
     * @param string $ns
     * @param string $subjType
     * @param string $subjId
     * @param string $objType
     * @param string $objId
     * @param string $relation
     * @param int $depth
     * @return bool
     */
    private function dfs(string $ns, string $subjType, string $subjId, string $objType, string $objId, string $relation, int $depth): bool
    {
        if ($depth > $this->maxDepth) return false;
        // direct tuples
        foreach ($this->store->readByObject($ns, $objType, $objId, $relation) as $t) {
            // direct match on subject
            if ($t->subjType === $subjType && $t->subjId === $subjId && $t->subjRel === null) return true;
            // indirect: subject reference "type:id#rel"
            if ($t->subjRel !== null) {
                // is subject a member of (type:id)#rel ?
                if ($this->dfs($ns, $subjType, $subjId, $t->subjType, $t->subjId, $t->subjRel, $depth + 1)) {
                    return true;
                }
            }
        }
        return false;
    }
}
