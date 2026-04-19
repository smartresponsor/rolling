<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony;

use App\Infrastructure\Symfony\DependencyInjection\RoleExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class RoleBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new RoleExtension();
        }

        return $this->extension;
    }
}
