<?php

namespace Pok\Bundle\PoolDBMBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('pok_pool_dbm');

        $rootNode
            ->children()
                ->arrayNode('managers')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('id')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('auto_mapping')->defaultFalse()->end()
                ->arrayNode('mappings')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->beforeNormalization()
                            ->ifString()
                            ->then(function($v) { return array ('type' => $v); })
                        ->end()
                        ->treatNullLike(array())
                        ->treatFalseLike(array('mapping' => false))
                        ->performNoDeepMerging()
                        ->children()
                            ->scalarNode('mapping')->defaultValue(true)->end()
                            ->scalarNode('type')->end()
                            ->scalarNode('dir')->end()
                            ->scalarNode('prefix')->end()
                            ->scalarNode('alias')->end()
                            ->booleanNode('is_bundle')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
