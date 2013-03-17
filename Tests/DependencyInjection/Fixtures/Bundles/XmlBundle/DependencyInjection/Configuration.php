<?php

namespace Pok\Bundle\DoctrineMultiBundle\Tests\DependencyInjection\Fixtures\Bundles\XmlBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('xml');

        return $treeBuilder;
    }
}
