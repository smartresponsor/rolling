<?php
declare(strict_types=1);

namespace Http\Role\SymfonyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 *
 */

/**
 *
 */
final class Configuration implements ConfigurationInterface
{
    /**
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('role');
        $root = $treeBuilder->getRootNode();
        $root
            ->children()
            ->scalarNode('endpoint')->defaultValue('http://localhost:8088/v2')->end()
            ->scalarNode('hmac_key')->defaultNull()->end()
            ->integerNode('timeout_ms')->defaultValue(800)->min(50)->end()
            ->end();
        return $treeBuilder;
    }
}
