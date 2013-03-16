<?php

namespace Pok\Bundle\DoctrineMultiBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

use Pok\Bundle\DoctrineMultiBundle\DependencyInjection\PokDoctrineMultiExtension;

abstract class AbstractDoctrineMultiExtensionTest extends \PHPUnit_Framework_TestCase
{
    abstract protected function loadFromFile(ContainerBuilder $container, $file);

    public function testDependencyInjectionConfigurationDefaults()
    {
        $container = $this->getContainer();
        $loader = new PokDoctrineMultiExtension();

        $loader->load(array(array()), $container);

        $this->assertEquals('Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain', $container->getParameter('pok.doctrine_multi.metadata.driver_chain.class'));
        $this->assertEquals('Pok\Bundle\DoctrineMultiBundle\Mapping\Driver\XmlDriver', $container->getParameter('pok.doctrine_multi.metadata.xml.class'));
    }

    public function testXmlBundleMappingDetection()
    {
        $container = $this->getContainer('XmlBundle');
        $loader = new PokDoctrineMultiExtension();

        $loader->load(array(array('mappings' => array('XmlBundle' => array()))), $container);

        $calls = $container->getDefinition('pok.doctrine_multi.default_metadata_driver')->getMethodCalls();
        $this->assertEquals('pok.doctrine_multi.default_xml_metadata_driver', (string) $calls[0][1][0]);
        $this->assertEquals('Pok\Bundle\DoctrineMultiBundle\Tests\DependencyInjection\Fixtures\Bundles\XmlBundle\MultiModel', $calls[0][1][1]);
    }

    protected function getContainer($bundle = 'XmlBundle')
    {
        require_once __DIR__.'/Fixtures/Bundles/'.$bundle.'/'.$bundle.'.php';

        return new ContainerBuilder(new ParameterBag(array(
            'kernel.bundles'          => array($bundle => 'Pok\\Bundle\\DoctrineMultiBundle\\Tests\\DependencyInjection\\Fixtures\\Bundles\\'.$bundle.'\\'.$bundle),
            'kernel.cache_dir'        => __DIR__,
            'kernel.compiled_classes' => array(),
            'kernel.debug'            => false,
            'kernel.environment'      => 'test',
            'kernel.name'             => 'kernel',
            'kernel.root_dir'         => __DIR__,
        )));
    }
}
