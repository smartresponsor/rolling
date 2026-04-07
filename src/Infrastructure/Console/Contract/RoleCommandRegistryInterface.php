<?php

declare(strict_types=1);

namespace App\Infrastructure\Console\Contract;

use Symfony\Component\Console\Command\Command;

interface RoleCommandRegistryInterface
{
    /**
     * @return list<Command>
     */
    public function all(): array;
}
