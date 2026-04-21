<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Console\Contract;

use Symfony\Component\Console\Command\Command;

interface RoleCommandFactoryInterface
{
    /**
     * @return list<Command>
     */
    public function createCommands(): array;
}
