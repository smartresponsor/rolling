<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Symfony\DependencyInjection\Compiler;

use App\Rolling\Service\Policy\PolicyEngine;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterPolicyVotersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(PolicyEngine::class) && !$container->hasAlias(PolicyEngine::class)) {
            return;
        }

        $definition = $container->findDefinition(PolicyEngine::class);
        $taggedServices = $container->findTaggedServiceIds('rolling.policy_voter');
        foreach (array_keys($taggedServices) as $serviceId) {
            $definition->addMethodCall('addVoter', [new Reference($serviceId)]);
        }
    }
}
