<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Symfony;

use App\Rolling\Infrastructure\Symfony\DependencyInjection\RoleExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class RoleBundle extends Bundle
{
    public function getPath(): string
    {
        return dirname(__DIR__, 3);
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new RoleExtension();
        }

        return $this->extension;
    }
}
