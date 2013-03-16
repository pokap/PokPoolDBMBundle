<?php

namespace Pok\Bundle\DoctrineMultiBundle\Tests\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Pok\Bundle\DoctrineMultiBundle\DependencyInjection\PokDoctrineMultiExtension;

class PokDoctrineMultiExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testParameterAndAliasManagers()
    {
        $container = new ContainerBuilder();

        $container->set('doctrine.orm.entity_manager', null);
        $container->set('doctrine_mongodb.odm.document_manager', null);

        $value = array(
            'orm' => array('id' => 'doctrine.orm.entity_manager'),
            'odm' => array('id' => 'doctrine_mongodb.odm.document_manager'),
        );

        $loader = new PokDoctrineMultiExtension();
        $loader->load(array(array('managers' => $value)), $container);

        $alias = array();
        foreach ($value as $name => $data) {
            $alias[$name] = 'pok.doctrine_multi.manager.'.$name;
            $this->assertEquals($data['id'], $container->getAlias($alias[$name]));
        }

        $this->assertEquals($alias, $container->getParameter('pok.doctrine_multi.managers'));
    }
}
