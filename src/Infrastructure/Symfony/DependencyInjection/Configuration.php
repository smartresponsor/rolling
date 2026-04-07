<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $tree = new TreeBuilder('role');
        $root = $tree->getRootNode();
        $root
            ->children()
                ->arrayNode('pdp')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->enumNode('mode')->values(['remote', 'inproc', 'cached'])->defaultValue('remote')->end()
                        ->arrayNode('remote')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('base_url')->defaultValue('http://localhost')->end()
                                ->scalarNode('api_key')->defaultNull()->end()
                                ->scalarNode('hmac_secret')->defaultNull()->end()
                                ->integerNode('timeout_ms')->defaultValue(300)->end()
                                ->integerNode('retries')->defaultValue(2)->end()
                            ->end()
                        ->end()
                        ->arrayNode('cache')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')->defaultTrue()->end()
                                ->integerNode('ttl_seconds')->defaultValue(600)->end()
                            ->end()
                        ->end()
                        ->arrayNode('registry')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')->defaultTrue()->end()
                                ->scalarNode('path')->defaultValue('%kernel.project_dir%/ops/policy/registry.json')->end()
                            ->end()
                        ->end()
                        ->arrayNode('audit')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')->defaultTrue()->end()
                                ->scalarNode('pdo_dsn')->defaultNull()->end()
                                ->scalarNode('pdo_user')->defaultNull()->end()
                                ->scalarNode('pdo_pass')->defaultNull()->end()
                            ->end()
                        ->end()
                        ->arrayNode('metrics')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')->defaultTrue()->end()
                                ->scalarNode('component')->defaultValue('remote')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('security')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('hmac_enabled')->defaultTrue()->end()
                        ->scalarNode('hmac_secret')->defaultNull()->end()
                        ->integerNode('allowed_skew_sec')->defaultValue(300)->end()
                        ->arrayNode('anti_replay')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')->defaultTrue()->end()
                                ->integerNode('ttl_sec')->defaultValue(600)->end()
                                ->scalarNode('pdo_dsn')->defaultNull()->end()
                                ->scalarNode('pdo_user')->defaultNull()->end()
                                ->scalarNode('pdo_pass')->defaultNull()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $tree;
    }
}
