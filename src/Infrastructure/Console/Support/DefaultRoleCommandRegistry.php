<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Console\Support;

use App\Rolling\Infrastructure\Console\Contract\RoleCommandFactoryInterface;
use App\Rolling\Infrastructure\Console\Contract\RoleCommandRegistryInterface;

final class DefaultRoleCommandRegistry implements RoleCommandRegistryInterface
{
    public function __construct(private readonly RoleCommandFactoryInterface $factory)
    {
    }

    public function all(): array
    {
        return $this->factory->createCommands();
    }
}
