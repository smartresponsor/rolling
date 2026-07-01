<?php

declare(strict_types=1);

namespace App\Rolling\InfrastructureInterface\Rebac;

use App\Rolling\Infrastructure\Rebac\Tuple;
use App\Rolling\Service\Consistency\Rebac\RebacConsistencyToken;

/** Tuple persistence + revision token. */
interface TupleStoreInterface
{
    public function write(string $ns, array $tuples): RebacConsistencyToken;

    public function delete(string $ns, Tuple $tuple): RebacConsistencyToken;

    /**
     * @return iterable<Tuple>
     */
    public function readByObject(string $ns, string $objType, string $objId, string $relation): iterable;

    /**
     * @return iterable<Tuple>
     */
    public function readBySubject(string $ns, string $subjType, string $subjId, ?string $subjRel = null): iterable;

    public function currentToken(): RebacConsistencyToken;
}
