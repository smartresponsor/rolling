<?php
declare(strict_types=1);

namespace App\Service\Role\Rebac;

use App\Consistency\Role\Rebac\Token;
use App\Model\Role\Rebac\Tuple;
use App\Store\Role\Rebac\TupleStoreInterface;

/**
 *
 */

/**
 *
 */
final class Writer
{
    /**
     * @param \App\Store\Role\Rebac\TupleStoreInterface $store
     */
    public function __construct(private readonly TupleStoreInterface $store)
    {
    }

    /**
     * @param string $ns
     * @param array $tuples
     * @return \App\Consistency\Role\Rebac\Token
     */
    public function write(string $ns, array $tuples): Token
    {
        return $this->store->write($ns, $tuples);
    }

    /**
     * @param string $ns
     * @param \App\Model\Role\Rebac\Tuple $tuple
     * @return \App\Consistency\Role\Rebac\Token
     */
    public function delete(string $ns, Tuple $tuple): Token
    {
        return $this->store->delete($ns, $tuple);
    }
}
