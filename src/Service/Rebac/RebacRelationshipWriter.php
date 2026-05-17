<?php

declare(strict_types=1);

namespace App\Rolling\Service\Rebac;

use App\Rolling\Infrastructure\Rebac\Tuple;
use App\Rolling\InfrastructureInterface\Rebac\TupleStoreInterface;
use App\Rolling\Service\Consistency\Rebac\RebacConsistencyToken;

final class RebacRelationshipWriter
{
    /**
     * @param TupleStoreInterface $store
     */
    public function __construct(private readonly TupleStoreInterface $store)
    {
    }

    /**
     * @param string $ns
     * @param array  $tuples
     *
     * @return RebacConsistencyToken
     */
    public function write(string $ns, array $tuples): RebacConsistencyToken
    {
        return $this->store->write($ns, $tuples);
    }

    /**
     * @param string $ns
     * @param Tuple  $tuple
     *
     * @return RebacConsistencyToken
     */
    public function delete(string $ns, Tuple $tuple): RebacConsistencyToken
    {
        return $this->store->delete($ns, $tuple);
    }
}
