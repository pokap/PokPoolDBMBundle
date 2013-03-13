<?php

namespace Pok\Bundle\DoctrineMultiBundle\Tests\Persisters;

use Pok\Bundle\DoctrineMultiBundle\Persisters\ModelPersister;
use Pok\Bundle\DoctrineMultiBundle\ModelManager;
use Pok\Bundle\DoctrineMultiBundle\UnitOfWork;

class ModelPersisterTest extends \PHPUnit_Framework_TestCase
{
    public function test__construct()
    {
        $metadata = new \Pok\Bundle\DoctrineMultiBundle\Mapping\ClassMetadata(__NAMESPACE__ . '\\ModelTest');
        $metadata->addModel('entity', __NAMESPACE__ . '\\EntityTest', array());
        $metadata->addModel('document', __NAMESPACE__ . '\\DocumentTest', array());
        $metadata->setIdentifier('entity', 'id');

        $metadataFactory = $this->getMock('Pok\\Bundle\\DoctrineMultiBundle\\Mapping\\ClassMetadataFactory', array('getMetadataFor', 'setModelManager'));
        $metadataFactory->expects($this->any())->method('getMetadataFor')->will($this->returnValue($metadata));

        $manager = new ModelManager(array('entity' => new EntityManager(), 'document' => new DocumentManager()), $metadataFactory);
        $persisters = new ModelPersister($manager, new UnitOfWork($manager), $metadata);

        $this->assertInstanceOf('Pok\\Bundle\\DoctrineMultiBundle\\Mapping\\ClassMetadata', $persisters->getClassMetadata());

        $model = $persisters->load(array('id' => 1));
        $this->assertInstanceOf(__NAMESPACE__ . '\\ModelTest', $model);
        $this->assertInstanceOf(__NAMESPACE__ . '\\EntityTest', $model->entity);
        $this->assertEquals(1, $model->entity->id);
        $this->assertInstanceOf(__NAMESPACE__ . '\\DocumentTest', $model->document);
        $this->assertEquals(1, $model->document->id);

        $models = $persisters->loadAll();
        foreach ($models as $model) {
            $this->assertInstanceOf(__NAMESPACE__ . '\\ModelTest', $model);
            
            $this->assertInstanceOf(__NAMESPACE__ . '\\EntityTest', $model->entity);
            $this->assertInstanceOf(__NAMESPACE__ . '\\DocumentTest', $model->document);
            $this->assertEquals($model->entity->id, $model->document->id);
        }

        $this->assertEquals(4, EntityRepository::$count + DocumentRepository::$count);
    }
}

class ModelTest {
    public $entity;
    public $document;
}

class EntityTest {
    public $id;
}

class DocumentTest {
    public $id;
}

class EntityManager {
    public function getRepository($entityClass) {
        return new EntityRepository();
    }
}

class DocumentManager {
    public function getRepository($documentClass) {
        return new DocumentRepository();
    }
}

class EntityRepository {
    public static $count = 0;

    public function findOneBy(array $criteria) {
        self::$count++;

        $entity = new EntityTest;
        $entity->id = $criteria['id'];

        return $entity;
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null) {
        self::$count++;

        $list = array();
        foreach (range(1, 5) as $id) {
            $entity = new EntityTest;
            $entity->id = $id;

            $list[] = $entity;
        }

        return $list;
    }
}

class DocumentRepository {
    public static $count = 0;

    public function find($id) {
        self::$count++;

        $document = new DocumentTest;
        $document->id = $id;

        return $document;
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null) {
        self::$count++;

        $list = array();
        foreach ($criteria['id'] as $id) {
            $document = new DocumentTest;
            $document->id = $id;

            $list[] = $document;
        }

        return $list;
    }
}
