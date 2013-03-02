<?php

namespace Pok\Bundle\DoctrineMultiBundle\Tests\Mapping;

use Pok\Bundle\DoctrineMultiBundle\Mapping\ClassMetadata;

abstract class AbstractMappingDriverTest extends \PHPUnit_Framework_TestCase
{
    abstract protected function _loadDriver();

    public function testLoadMapping()
    {
        $className = __NAMESPACE__.'\Test';
        $mappingDriver = $this->_loadDriver();

        $class = new ClassMetadata($className);
        $mappingDriver->loadMetadataForClass($className, $class);

        return $class;
    }

    /**
     * @depends testLoadMapping
     * @param ClassMetadata $class
     */
    public function testFieldMappings($class)
    {
        $this->assertEquals(2, count($class->fieldMappings));

        $this->assertTrue(isset($class->fieldMappings['entity']));
        $this->assertTrue(isset($class->fieldMappings['document']));

        $this->assertEquals(array('name'), $class->fieldMappings['entity']['fields']);
        $this->assertEquals(array('profileContent'), $class->fieldMappings['document']['fields']);

        return $class;
    }

    /**
     * @depends testFieldMappings
     * @param ClassMetadata $class
     */
    public function testIdentifier($class)
    {
        $this->assertEquals('id', $class->identifier['field']);
        $this->assertEquals('document', $class->identifier['manager']);

        return $class;
    }
}

class Test
{
    private $entity;

    private $document;

    public function getId()
    {
        return $this->document->getId();
    }

    public function getName()
    {
        return $this->entity->getName();
    }

    public function getProfileContent()
    {
        return $this->document->getProfileContent();
    }
}
