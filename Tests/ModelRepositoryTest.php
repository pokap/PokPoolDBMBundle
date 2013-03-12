<?php

namespace Pok\Bundle\DoctrineMultiBundle\Tests;

use Pok\Bundle\DoctrineMultiBundle\ModelRepository as Repository;

class ModelRepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetter()
    {
        $metadata = new \Pok\Bundle\DoctrineMultiBundle\Mapping\ClassMetadata(__NAMESPACE__ . '\\FakeModelTest');
        $metadata->addModel('fake', __NAMESPACE__ . '\\FakeTest', array());

        $manager = $this->getMockBuilder('Pok\\Bundle\\DoctrineMultiBundle\\ModelManager')->disableOriginalConstructor()->getMock();
        $manager->expects($this->any())->method('createQueryBuilder')->will($this->returnValue(true));

        $persist = $this->getMockBuilder('Pok\\Bundle\\DoctrineMultiBundle\\Persisters\\ModelPersister')->disableOriginalConstructor()->getMock();
        $persist->expects($this->any())->method('load')->will($this->returnValue(true));
        $persist->expects($this->any())->method('loadAll')->will($this->returnValue(true));

        $unit = $this->getMockBuilder('Pok\\Bundle\\DoctrineMultiBundle\\UnitOfWork')->disableOriginalConstructor()->getMock();
        $unit->expects($this->any())->method('getMultiModelPersister')->will($this->returnValue($persist));

        $repo = new Repository($manager, $unit, $metadata);

        $this->assertTrue($repo->createQueryBuilder('fake'));
        $this->assertTrue($repo->find(null));
        $this->assertTrue($repo->findAll());
        $this->assertTrue($repo->findBy(array()));
        $this->assertTrue($repo->findOneBy(array()));
        $this->assertEquals(__NAMESPACE__ . '\\FakeModelTest', $repo->getClassName());
    }
}

class FakeModelTest {
    private $fake;
}
