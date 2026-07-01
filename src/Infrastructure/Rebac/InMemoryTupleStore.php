<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Rebac;

use App\Rolling\Service\Consistency\Rebac\RebacConsistencyToken;

final class InMemoryTupleStore implements \App\Rolling\InfrastructureInterface\Rebac\TupleStoreInterface
{
    private array $tuples = [];
    private int $rev = 0;

    public function write(string $ns, array $tuples): RebacConsistencyToken
    {
        foreach ($tuples as $t) {
            $this->tuples[] = $t;
        }
        ++$this->rev;

        return new RebacConsistencyToken($this->rev);
    }

    public function delete(string $ns, Tuple $tuple): RebacConsistencyToken
    {
        $subjectRelation = $tuple->subjRel;

        $this->tuples = array_values(array_filter($this->tuples, function (Tuple $t) use ($tuple, $ns, $subjectRelation): bool {
            return !($t->ns === $ns && $t->objType === $tuple->objType && $t->objId === $tuple->objId
                && $t->relation === $tuple->relation && $t->subjType === $tuple->subjType
                && $t->subjId === $tuple->subjId && $t->subjRel === $subjectRelation);
        }));
        ++$this->rev;

        return new RebacConsistencyToken($this->rev);
    }

    public function readByObject(string $ns, string $objType, string $objId, string $relation): iterable
    {
        foreach ($this->tuples as $t) {
            if ($t->ns === $ns && $t->objType === $objType && $t->objId === $objId && $t->relation === $relation) {
                yield $t;
            }
        }
    }

    public function readBySubject(string $ns, string $subjType, string $subjId, ?string $subjRel = null): iterable
    {
        foreach ($this->tuples as $t) {
            if ($t->ns === $ns && $t->subjType === $subjType && $t->subjId === $subjId && $t->subjRel === $subjRel) {
                yield $t;
            }
        }
    }

    public function currentToken(): RebacConsistencyToken
    {
        return new RebacConsistencyToken($this->rev);
    }
}
