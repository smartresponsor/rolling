<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

final class RoleExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('role.policy_namespace', $config['policy_namespace']);
        $container->setParameter('role.admin_namespace', $config['admin_namespace']);
        $container->setParameter('role.audit_namespace', $config['audit_namespace']);
        $container->setParameter('role.ops_dir', $config['ops_dir']);
        $container->setParameter('role.sdk_namespace', $config['sdk_namespace']);
    }

    public function getAlias(): string
    {
        return 'role';
    }
}
