<?php

namespace Pok\Bundle\PoolDBMBundle\Tests\Mapping\Driver;

use Doctrine\Common\Persistence\Mapping\Driver\FileDriver;

abstract class AbstractDriverTest extends \PHPUnit_Framework_TestCase
{
    public function testFindMappingFile()
    {
        $driver = $this->getDriver(array(
            'test' => 'MyNamespace\MyBundle\MultiModelTest',
            $this->getFixtureDir() => 'MyNamespace\MyBundle\MultiModel',
        ));

        $locator = $this->getDriverLocator($driver);

        $this->assertEquals(
            $this->getFixtureDir() . '/Test' . $this->getFileExtension(),
            $locator->findMappingFile('MyNamespace\MyBundle\MultiModel\Test')
        );
    }

    public function testFindMappingFileInSubnamespace()
    {
        $driver = $this->getDriver(array(
            $this->getFixtureDir() => 'MyNamespace\MyBundle\MultiModel',
        ));

        $locator = $this->getDriverLocator($driver);

        $this->assertEquals(
            $this->getFixtureDir() . '/Foo.Bar' . $this->getFileExtension(),
            $locator->findMappingFile('MyNamespace\MyBundle\MultiModel\Foo\Bar')
        );
    }

    /**
     * @expectedException Doctrine\Common\Persistence\Mapping\MappingException
     */
    public function testFindMappingFileNamespacedFoundFileNotFound()
    {
        $driver = $this->getDriver(array(
            $this->getFixtureDir() => 'MyNamespace\MyBundle\MultiModel',
        ));

        $locator = $this->getDriverLocator($driver);
        $locator->findMappingFile('MyNamespace\MyBundle\MultiModel\Missing');
    }

    /**
     * @expectedException Doctrine\Common\Persistence\Mapping\MappingException
     */
    public function testFindMappingNamespaceNotFound()
    {
        $driver = $this->getDriver(array(
            $this->getFixtureDir() => 'MyNamespace\MyBundle\MultiModel',
        ));

        $locator = $this->getDriverLocator($driver);
        $locator->findMappingFile('MyOtherNamespace\MyBundle\MultiModel\Test');
    }

    abstract protected function getFileExtension();
    abstract protected function getFixtureDir();
    abstract protected function getDriver(array $paths = array());

    private function getDriverLocator(FileDriver $driver)
    {
        $ref = new \ReflectionProperty($driver, 'locator');
        $ref->setAccessible(true);

        return $ref->getValue($driver);
    }
}
