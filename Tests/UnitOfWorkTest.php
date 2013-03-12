<?php

namespace Pok\Bundle\DoctrineMultiBundle\Tests;

use Pok\Bundle\DoctrineMultiBundle\UnitOfWork;

class UnitOfWorkTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateMultiModel()
    {
        $metadata = new \Pok\Bundle\DoctrineMultiBundle\Mapping\ClassMetadata(__NAMESPACE__ . '\\FakeModelTest2');
        $metadata->addModel('fake', __NAMESPACE__ . '\\FakeTest', array());

        $manager = $this->getMockBuilder('Pok\\Bundle\\DoctrineMultiBundle\\ModelManager')->disableOriginalConstructor()->getMock();
        $manager->expects($this->any())->method('getClassMetadata')->will($this->returnValue($metadata));

        $unit = new UnitOfWork($manager);

        $model = $unit->createMultiModel(__NAMESPACE__ . '\\FakeModelTest2', array('fake' => new FakeTest));

        $this->assertInstanceOf(__NAMESPACE__ . '\\FakeModelTest2', $model);
        $this->assertInstanceOf(__NAMESPACE__ . '\\FakeTest', $model->getFake());

        $this->assertInstanceOf('Pok\\Bundle\\DoctrineMultiBundle\\Persisters\\ModelPersister', $unit->getMultiModelPersister(__NAMESPACE__ . '\\FakeModelTest2'));
    }

    public function testPropertyChanged()
    {
        $manager = $this->getMockBuilder('Pok\\Bundle\\DoctrineMultiBundle\\ModelManager')->disableOriginalConstructor()->getMock();
        $unit    = new UnitOfWork($manager);

        try {
            $unit->propertyChanged('fake', 'fake', false, true);
        } catch (\Exception $e) {
            $this->assertInstanceOf('LogicException', $e);
            $this->assertEquals('Use directely model to change property.', $e->getMessage());
        }
    }
}

class FakeModelTest2 {
    private $fake;

    public function getFake() {
        return $this->fake;
    }
}

class FakeTest {}
