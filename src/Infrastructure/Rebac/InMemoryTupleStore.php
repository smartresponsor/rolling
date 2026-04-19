<?php

declare(strict_types=1);

namespace App\Infrastructure\Rebac;

use App\Service\Consistency\Rebac\Token;

final class InMemoryTupleStore implements \App\InfrastructureInterface\Rebac\TupleStoreInterface
{
    /** @var array */
    private array $tuples = [];
    private int $rev = 0;

    /**
     * @param string $ns
     * @param array  $tuples
     *
     * @return Token
     */
    public function write(string $ns, array $tuples): Token
    {
        foreach ($tuples as $t) {
            $this->tuples[] = $t;
        }
        ++$this->rev;

        return new Token($this->rev);
    }

    /**
     * @param string $ns
     * @param Tuple  $tuple
     *
     * @return Token
     */
    public function delete(string $ns, Tuple $tuple): Token
    {
        $subjectRelation = $tuple->subjRel;

        $this->tuples = array_values(array_filter($this->tuples, function (Tuple $t) use ($tuple, $ns, $subjectRelation): bool {
            return !($t->ns === $ns && $t->objType === $tuple->objType && $t->objId === $tuple->objId
                && $t->relation === $tuple->relation && $t->subjType === $tuple->subjType
                && $t->subjId === $tuple->subjId && $t->subjRel === $subjectRelation);
        }));
        ++$this->rev;

        return new Token($this->rev);
    }

    /**
     * @param string $ns
     * @param string $objType
     * @param string $objId
     * @param string $relation
     *
     * @return iterable
     */
    public function readByObject(string $ns, string $objType, string $objId, string $relation): iterable
    {
        foreach ($this->tuples as $t) {
            if ($t->ns === $ns && $t->objType === $objType && $t->objId === $objId && $t->relation === $relation) {
                yield $t;
            }
        }
    }

    /**
     * @param string      $ns
     * @param string      $subjType
     * @param string      $subjId
     * @param string|null $subjRel
     *
     * @return iterable
     */
    public function readBySubject(string $ns, string $subjType, string $subjId, ?string $subjRel = null): iterable
    {
        foreach ($this->tuples as $t) {
            if ($t->ns === $ns && $t->subjType === $subjType && $t->subjId === $subjId && $t->subjRel === $subjRel) {
                yield $t;
            }
        }
    }

    /**
     * @return Token
     */
    public function currentToken(): Token
    {
        return new Token($this->rev);
    }
}
