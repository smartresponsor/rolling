<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Symfony\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class RoleExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if (($config['enabled'] ?? true) !== true) {
            return;
        }

        $container->setParameter('role.policy_namespace', $config['policy_namespace']);
        $container->setParameter('role.admin_namespace', $config['admin_namespace']);
        $container->setParameter('role.audit_namespace', $config['audit_namespace']);
        $container->setParameter('role.ops_dir', $config['ops_dir']);
        $container->setParameter('role.sdk_namespace', $config['sdk_namespace']);

        $loader = new YamlFileLoader($container, new FileLocator(dirname(__DIR__, 4).'/config'));
        $loader->load('services.yaml');
    }

    public function getAlias(): string
    {
        return 'role';
    }
}
