<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('role');

        $treeBuilder->getRootNode()
            ->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('enabled')->defaultTrue()->end()
                ->scalarNode('policy_namespace')->defaultValue('role')->cannotBeEmpty()->end()
                ->scalarNode('admin_namespace')->defaultValue('role-admin')->cannotBeEmpty()->end()
                ->scalarNode('audit_namespace')->defaultValue('role-audit')->cannotBeEmpty()->end()
                ->scalarNode('ops_dir')->defaultValue('%kernel.project_dir%/ops')->cannotBeEmpty()->end()
                ->scalarNode('sdk_namespace')->defaultValue('Rolling\\SDK\\V2')->cannotBeEmpty()->end()
            ->end();

        return $treeBuilder;
    }
}
