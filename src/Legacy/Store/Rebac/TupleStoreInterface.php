<?php

declare(strict_types=1);

namespace App\Legacy\Store\Rebac;

use App\Legacy\Model\Rebac\Tuple;
use App\Legacy\Consistency\Rebac\Token;

/** Tuple persistence + revision token. */
interface TupleStoreInterface
{
    /**
     * @param string $ns
     * @param array $tuples
     * @return \App\Legacy\Consistency\Rebac\Token
     */
    public function write(string $ns, array $tuples): Token;

    /**
     * @param string $ns
     * @param \App\Legacy\Model\Rebac\Tuple $tuple
     * @return \App\Legacy\Consistency\Rebac\Token
     */
    public function delete(string $ns, Tuple $tuple): Token;

    /**
     * @param string $ns
     * @param string $objType
     * @param string $objId
     * @param string $relation
     * @return iterable<Tuple>
     */
    public function readByObject(string $ns, string $objType, string $objId, string $relation): iterable;

    /**
     * @param string $ns
     * @param string $subjType
     * @param string $subjId
     * @param string|null $subjRel
     * @return iterable<Tuple>
     */
    public function readBySubject(string $ns, string $subjType, string $subjId, ?string $subjRel = null): iterable;

    /**
     * @return \App\Legacy\Consistency\Rebac\Token
     */
    public function currentToken(): Token;
}
