<?php

namespace Pok\Bundle\PoolDBMBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

use Pok\Bundle\PoolDBMBundle\DependencyInjection\PokPoolDBMExtension;

class PokPoolDBMExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testParameterAndAliasManagers()
    {
        $container = new ContainerBuilder();

        $container->set('doctrine.orm.entity_manager', new FakeService());
        $container->set('doctrine_mongodb.odm.document_manager', new FakeService());

        $value = array(
            'orm' => array('id' => 'doctrine.orm.entity_manager'),
            'odm' => array('id' => 'doctrine_mongodb.odm.document_manager'),
        );

        $loader = new PokPoolDBMExtension();
        $loader->load(array(array('managers' => $value)), $container);

        $alias = array();
        foreach ($value as $name => $data) {
            $alias[$name] = 'pok.pool_dbm.manager.'.$name;
            $this->assertEquals($data['id'], $container->getAlias($alias[$name]));
        }

        $this->assertEquals($alias, $container->getParameter('pok.pool_dbm.managers'));
    }
}

class FakeService
{
}
