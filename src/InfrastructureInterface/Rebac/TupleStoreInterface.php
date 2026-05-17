<?php

declare(strict_types=1);

namespace App\Rolling\InfrastructureInterface\Rebac;

use App\Rolling\Infrastructure\Rebac\Tuple;
use App\Rolling\Service\Consistency\Rebac\RebacConsistencyToken;

/** Tuple persistence + revision token. */
interface TupleStoreInterface
{
    /**
     * @param string $ns
     * @param array  $tuples
     *
     * @return RebacConsistencyToken
     */
    public function write(string $ns, array $tuples): RebacConsistencyToken;

    /**
     * @param string $ns
     * @param Tuple  $tuple
     *
     * @return RebacConsistencyToken
     */
    public function delete(string $ns, Tuple $tuple): RebacConsistencyToken;

    /**
     * @param string $ns
     * @param string $objType
     * @param string $objId
     * @param string $relation
     *
     * @return iterable<Tuple>
     */
    public function readByObject(string $ns, string $objType, string $objId, string $relation): iterable;

    /**
     * @param string      $ns
     * @param string      $subjType
     * @param string      $subjId
     * @param string|null $subjRel
     *
     * @return iterable<Tuple>
     */
    public function readBySubject(string $ns, string $subjType, string $subjId, ?string $subjRel = null): iterable;

    /**
     * @return RebacConsistencyToken
     */
    public function currentToken(): RebacConsistencyToken;
}
