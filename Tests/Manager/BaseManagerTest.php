<?php

namespace Pok\Bundle\DoctrineMultiBundle\Tests\Manager;

use Pok\Bundle\DoctrineMultiBundle\ModelManager;
use Pok\Bundle\DoctrineMultiBundle\Manager\BaseManager;

class BaseManagerTest extends \PHPUnit_Framework_TestCase
{
    public function test__construct()
    {
        $metadata = new \Pok\Bundle\DoctrineMultiBundle\Mapping\ClassMetadata(__NAMESPACE__ . '\\ModelTest');
        $metadata->addModel('entity', __NAMESPACE__ . '\\EntityTest', array());
        $metadata->setIdentifier('entity', 'id');

        $metadataFactory = $this->getMock('Pok\\Bundle\\DoctrineMultiBundle\\Mapping\\ClassMetadataFactory', array('getMetadataFor', 'setModelManager'));
        $metadataFactory->expects($this->any())->method('getMetadataFor')->will($this->returnValue($metadata));

        $refl = new \ReflectionClass('Pok\\Bundle\\DoctrineMultiBundle\\Manager\\BaseManager');
        $this->assertTrue($refl->isAbstract());

        $manager = new TestManager(__NAMESPACE__ . '\\ModelTest', new ModelManager(array('entity' => new EntityManager()), $metadataFactory));
        $refl = new \ReflectionClass(get_class($manager));

        $repo = $refl->getMethod('getRepository');
        $this->assertTrue($repo->isProtected());
        $repo->setAccessible(true);
        $this->assertInstanceOf('Pok\\Bundle\\DoctrineMultiBundle\\ModelRepository', $repo->invoke($manager));

        $this->assertInstanceOf(__NAMESPACE__ . '\\ModelTest', $manager->create());

        $manager->save(new ModelTest());
        $manager->save(new ModelTest(), true);

        try {
            $manager->save(new \stdClass());
        } catch (\RuntimeException $e) {
            $this->assertEquals('Manager "Pok\Bundle\DoctrineMultiBundle\Tests\Manager\TestManager" is unable to save model "stdClass"',$e->getMessage());
        }

        $manager->clear();

        $this->assertInstanceOf(__NAMESPACE__ . '\\ModelTest', $manager->find(null));
        $this->assertEquals(1, count($manager->findBy(array())));
        $this->assertInstanceOf(__NAMESPACE__ . '\\ModelTest', $manager->findOneBy(array()));
        $this->assertEquals(1, count($manager->findAll()));
    }
}

class ModelTest {
    private $entity = '$ENTITYCLASS';
}

class EntityTest {
    private $id;
}

class EntityManager extends \PHPUnit_Framework_TestCase {
    public function getRepository($entityClass) {
        return new EntityRepository();
    }

    public function persist($entity) {
        $this->assertEquals('$ENTITYCLASS', $entity);
    }

    public function remove($entity) {
        $this->assertEquals('$ENTITYCLASS', $entity);
    }

    public function flush($entity = null) {
        $this->assertNull($entity);
    }

    public function clear($entity = null) {
        $this->assertNull($entity);
    }
}

class EntityRepository {
    public function find($id) {
        return new EntityTest();
    }

    public function findBy(array $criteria, array $order = null, $limit = null, $offset = null) {
        return array(new EntityTest());
    }

    public function findOneBy(array $criteria) {
        return new EntityTest();
    }

    public function findAll() {
        return array(new EntityTest());
    }
}

class TestManager extends BaseManager {}
