<?php

declare(strict_types=1);

namespace App\Rolling;

use App\Rolling\Infrastructure\Symfony\DependencyInjection\Compiler\RegisterPolicyVotersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Symfony bundle facade for the Rolling component.
 */
final class RollingBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterPolicyVotersPass());
    }
}
