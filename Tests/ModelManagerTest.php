<?php

namespace Pok\Bundle\DoctrineMultiBundle\Tests;

use Pok\Bundle\DoctrineMultiBundle\ModelManager;

class ModelManagerTest extends \PHPUnit_Framework_TestCase
{
    public function test__construct()
    {
        $metadata = new \Pok\Bundle\DoctrineMultiBundle\Mapping\ClassMetadata(__NAMESPACE__ . '\\ModelTest');
        $metadata->addModel('entity', __NAMESPACE__ . '\\EntityTest', array());
        $metadata->setIdentifier('entity', 'id');

        $metadataFactory = $this->getMock('Pok\\Bundle\\DoctrineMultiBundle\\Mapping\\ClassMetadataFactory', array('getMetadataFor', 'setModelManager'));
        $metadataFactory
            ->expects($this->any())
            ->method('getMetadataFor')
            ->will($this->returnValue($metadata));

        $manager = new ModelManager(array('entity' => new EntityManager()), $metadataFactory);

        $this->assertInstanceOf('Pok\\Bundle\\DoctrineMultiBundle\\Mapping\\ClassMetadataFactory', $manager->getMetadataFactory());
        $this->assertEquals(array('entity'), array_keys($manager->getManagers()));
        $this->assertInstanceOf(get_class($metadata), $manager->getClassMetadata(__NAMESPACE__ . '\\ModelTest'));

        $this->assertTrue($manager->createQueryBuilder(__NAMESPACE__ . '\\ModelTest', 'test'));

        $this->assertInstanceOf('Pok\\Bundle\\DoctrineMultiBundle\\ModelRepository', $manager->getRepository(__NAMESPACE__ . '\\ModelTest'));

        $this->assertTrue($manager->contains(new ModelTest));
    }

    public function testCustomRepository()
    {
        $metadata = new \Pok\Bundle\DoctrineMultiBundle\Mapping\ClassMetadata(__NAMESPACE__ . '\\ModelTest');
        $metadata->setCustomRepositoryClass(__NAMESPACE__ . '\\ModelRepository');

        $metadataFactory = $this->getMock('Pok\\Bundle\\DoctrineMultiBundle\\Mapping\\ClassMetadataFactory', array('getMetadataFor', 'setModelManager'));
        $metadataFactory
            ->expects($this->any())
            ->method('getMetadataFor')
            ->will($this->returnValue($metadata));

        $manager = new ModelManager(array(), $metadataFactory);
        $this->assertInstanceOf(__NAMESPACE__ . '\\ModelRepository', $manager->getRepository(__NAMESPACE__ . '\\ModelTest'));

        $this->assertTrue($manager->find(__NAMESPACE__ . '\\ModelTest', null));
    }
}

class ModelTest
{
    private $entity;
}

class EntityTest
{
    private $id;
}

class EntityManager extends \PHPUnit_Framework_TestCase
{
    public function getRepository($entityClass)
    {
        $this->assertEquals(__NAMESPACE__ . '\\EntityTest', $entityClass);

        return new EntityRepository();
    }
}

class ModelRepository extends \Pok\Bundle\DoctrineMultiBundle\ModelRepository
{
    public function find($id)
    {
        return true;
    }
}

class EntityRepository
{
    public function createQueryBuilder($alias)
    {
        return true;
    }
}
