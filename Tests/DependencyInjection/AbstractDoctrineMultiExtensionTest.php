<?php

namespace Pok\Bundle\PoolDBMBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

use Pok\Bundle\PoolDBMBundle\DependencyInjection\PokPoolDBMExtension;

abstract class AbstractDoctrineMultiExtensionTest extends \PHPUnit_Framework_TestCase
{
    abstract protected function loadFromFile(ContainerBuilder $container, $file);

    public function testDependencyInjectionConfigurationDefaults()
    {
        $container = $this->getContainer();
        $loader = new PokPoolDBMExtension();

        $loader->load(array(array()), $container);

        $this->assertEquals('Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain', $container->getParameter('pok.pool_dbm.metadata.driver_chain.class'));
        $this->assertEquals('Pok\Bundle\PoolDBMBundle\Mapping\Driver\XmlDriver', $container->getParameter('pok.pool_dbm.metadata.xml.class'));
    }

    public function testXmlBundleMappingDetection()
    {
        $container = $this->getContainer('XmlBundle');
        $loader = new PokPoolDBMExtension();

        $loader->load(array(array('mappings' => array('XmlBundle' => array()))), $container);

        $calls = $container->getDefinition('pok.pool_dbm.default_metadata_driver')->getMethodCalls();
        $this->assertEquals('pok.pool_dbm.default_xml_metadata_driver', (string) $calls[0][1][0]);
        $this->assertEquals('Pok\Bundle\PoolDBMBundle\Tests\DependencyInjection\Fixtures\Bundles\XmlBundle\MultiModel', $calls[0][1][1]);
    }

    protected function getContainer($bundle = 'XmlBundle')
    {
        require_once __DIR__.'/Fixtures/Bundles/'.$bundle.'/'.$bundle.'.php';

        return new ContainerBuilder(new ParameterBag(array(
            'kernel.bundles'          => array($bundle => 'Pok\\Bundle\\PoolDBMBundle\\Tests\\DependencyInjection\\Fixtures\\Bundles\\'.$bundle.'\\'.$bundle),
            'kernel.cache_dir'        => __DIR__,
            'kernel.compiled_classes' => array(),
            'kernel.debug'            => false,
            'kernel.environment'      => 'test',
            'kernel.name'             => 'kernel',
            'kernel.root_dir'         => __DIR__,
        )));
    }
}
