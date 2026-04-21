<?php

declare(strict_types=1);

namespace App\Rolling\Service\Rebac;

use App\Rolling\Infrastructure\Rebac\Tuple;
use App\Rolling\InfrastructureInterface\Rebac\TupleStoreInterface;
use App\Rolling\Service\Consistency\Rebac\Token;

final class Writer
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
     * @return Token
     */
    public function write(string $ns, array $tuples): Token
    {
        return $this->store->write($ns, $tuples);
    }

    /**
     * @param string $ns
     * @param Tuple  $tuple
     *
     * @return Token
     */
    public function delete(string $ns, Tuple $tuple): Token
    {
        return $this->store->delete($ns, $tuple);
    }
}
