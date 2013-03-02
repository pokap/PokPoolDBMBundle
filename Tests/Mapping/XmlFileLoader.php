<?php

namespace Pok\Bundle\DoctrineMultiBundle\Tests\Loader;

use Symfony\Component\Config\FileLocator;

use Pok\Bundle\DoctrineMultiBundle\Loader\XmlFileLoader;

class XmlFileLoaderTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (!class_exists('Symfony\Component\Config\FileLocator')) {
            $this->markTestSkipped('The "Config" component is not available');
        }
    }

    public function testLoad()
    {
        $loader = new XmlFileLoader(new FileLocator(array(__DIR__.'/../Fixtures/xml')));
        $relationRepository = $loader->load('Test.multi.xml');

        $this->assertTrue($relationRepository->valid());

        $this->assertEquals(2, count($relationRepository->getFields()), 'Two fields are register');
        $this->assertEquals(2, count($relationRepository->getManagers()), 'Two managers are register');

        $this->assertEquals('doctrine_dbal', $relationRepository->get('title'));
        $this->assertEquals('doctrine_mongodb', $relationRepository->get('content'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider getPathsToInvalidFiles
     */
    public function testLoadThrowsExceptionWithInvalidFile($filePath)
    {
        $loader = new XmlFileLoader(new FileLocator(array(__DIR__.'/../Fixtures/xml')));
        $loader->load($filePath);
    }

    public function getPathsToInvalidFiles()
    {
        return array(array('nonvalid.xml'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Document types are not allowed.
     */
    public function testDocTypeIsNotAllowed()
    {
        $loader = new XmlFileLoader(new FileLocator(array(__DIR__.'/../Fixtures/xml')));
        $loader->load('withdoctype.xml');
    }
}
