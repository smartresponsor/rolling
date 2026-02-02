<?php
declare(strict_types=1);

namespace App\Store\Role\Rebac;

use App\Model\Role\Rebac\Tuple;
use App\Consistency\Role\Rebac\Token;

/**
 *
 */

/**
 *
 */
final class InMemoryTupleStore implements TupleStoreInterface
{
    /** @var array */
    private array $tuples = [];
    private int $rev = 0;

    /**
     * @param string $ns
     * @param array $tuples
     * @return \App\Consistency\Role\Rebac\Token
     */
    public function write(string $ns, array $tuples): Token
    {
        foreach ($tuples as $t) {
            $this->tuples[] = $t;
        }
        $this->rev++;
        return new Token($this->rev);
    }

    /**
     * @param string $ns
     * @param \App\Model\Role\Rebac\Tuple $tuple
     * @return \App\Consistency\Role\Rebac\Token
     */
    public function delete(string $ns, Tuple $tuple): Token
    {
        $this->tuples = array_values(array_filter($this->tuples, function (Tuple $t) use ($tuple, $ns): bool {
            return !($t->ns === $ns && $t->objType === $tuple->objType && $t->objId === $tuple->objId
                && $t->relation === $tuple->relation && $t->subjType === $tuple->subjType
                && $t->subjId === $tuple->subjId && $t->subjRel === $tuple->subjRel);
        }));
        $this->rev++;
        return new Token($this->rev);
    }

    /**
     * @param string $ns
     * @param string $objType
     * @param string $objId
     * @param string $relation
     * @return iterable
     */
    public function readByObject(string $ns, string $objType, string $objId, string $relation): iterable
    {
        foreach ($this->tuples as $t) {
            if ($t->ns === $ns && $t->objType === $objType && $t->objId === $objId && $t->relation === $relation) yield $t;
        }
    }

    /**
     * @param string $ns
     * @param string $subjType
     * @param string $subjId
     * @param string|null $subjRel
     * @return iterable
     */
    public function readBySubject(string $ns, string $subjType, string $subjId, ?string $subjRel = null): iterable
    {
        foreach ($this->tuples as $t) {
            if ($t->ns === $ns && $t->subjType === $subjType && $t->subjId === $subjId && $t->subjRel === $subjRel) yield $t;
        }
    }

    /**
     * @return \App\Consistency\Role\Rebac\Token
     */
    public function currentToken(): Token
    {
        return new Token($this->rev);
    }
}
