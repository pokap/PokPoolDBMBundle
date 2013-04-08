<?php

namespace Pok\Bundle\PoolDBMBundle\Tests\DependencyInjection\Fixtures\Bundles\XmlBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class XmlExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setDefinition('xml.fake.entity.manager', new Definition(__NAMESPACE__ . '\\FakeService'));
        $container->setDefinition('xml.fake.document.manager', new Definition(__NAMESPACE__ . '\\FakeService'));
    }
}
