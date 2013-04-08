<?php

namespace Pok\Bundle\PoolDBMBundle\Tests\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class XmlPoolDBMExtensionTest extends AbstractDoctrineMultiExtensionTest
{
    protected function loadFromFile(ContainerBuilder $container, $file)
    {
        $loadXml = new XmlFileLoader($container, new FileLocator(__DIR__.'/Fixtures/config/xml'));
        $loadXml->load($file.'.xml');
    }
}