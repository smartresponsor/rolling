<?php
declare(strict_types=1);

namespace App\Service\Rebac;

use App\Service\Consistency\Rebac\Token;
use App\Legacy\Model\Rebac\Tuple;
use App\InfrastructureInterface\Rebac\TupleStoreInterface;

/**
 *
 */

/**
 *
 */
final class Writer
{
    /**
     * @param \App\InfrastructureInterface\Rebac\TupleStoreInterface $store
     */
    public function __construct(private readonly TupleStoreInterface $store)
    {
    }

    /**
     * @param string $ns
     * @param array $tuples
     * @return \App\Service\Consistency\Rebac\Token
     */
    public function write(string $ns, array $tuples): Token
    {
        return $this->store->write($ns, $tuples);
    }

    /**
     * @param string $ns
     * @param \App\Legacy\Model\Rebac\Tuple $tuple
     * @return \App\Service\Consistency\Rebac\Token
     */
    public function delete(string $ns, Tuple $tuple): Token
    {
        return $this->store->delete($ns, $tuple);
    }
}
