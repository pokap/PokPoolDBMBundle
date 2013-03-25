<?php

namespace Pok\Bundle\DoctrineMultiBundle\Tests\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Pok\Bundle\DoctrineMultiBundle\Command\GenerateMultiModelCommand;

class GenerateMultiModelCommandTest extends WebTestCase
{
    public function testExecute()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->add(new GenerateMultiModelCommand());

        $command = $application->find('pok:doctrine:multi:generate');

        $dialog = $this->getMock('Symfony\Component\Console\Helper\DialogHelper', array('askConfirmation'));
        $dialog->expects($this->any())->method('askConfirmation')->will($this->returnValue(true));

        $command->getHelperSet()->set($dialog, 'dialog');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $filesystem = new Filesystem();
        $this->assertTrue($filesystem->exists(__DIR__ . '/../DependencyInjection/Fixtures/Bundles/XmlBundle/MultiModel/Test.php'));
    }

    /**
     * @dataProvider getMetadataInfo
     */
    public function testBuildParameters($namespace, $metadata)
    {
        $refl = new \ReflectionClass('Pok\\Bundle\\DoctrineMultiBundle\\Command\\GenerateMultiModelCommand');
        $method = $refl->getMethod('buildParameters');
        $method->setAccessible(true);

        $parameters = array(
            'model_namespace' => $namespace . '\MultiModel',
            'model_name'      => 'Test',
            'managers'        => array(
                'entity' => array(
                    'namespace' => '\\' . $namespace . '\Entity\Test',
                    'methods'   => array(
                        array(
                            'comment'   => false,
                            'name'      => 'getId',
                            'type'      => 'getter',
                            'arguments' => array()
                        ),
                        array(
                            'comment'   => false,
                            'name'      => 'setName',
                            'type'      => 'setter',
                            'arguments' => array('$name')
                        ),
                        array(
                            'comment'   => false,
                            'name'      => 'getName',
                            'type'      => 'getter',
                            'arguments' => array()
                        )
                    )
                ),
                'document' => array(
                    'namespace' => '\\' . $namespace . '\Document\Test',
                    'methods'   => array(
                        array(
                            'comment'   => '/**
     * @param string $profileContent
     *
     * @return Test
     */',
                            'name'      => 'setProfileContent',
                            'type'      => 'setter',
                            'arguments' => array('$profileContent')
                        ),
                        array(
                            'comment'   => false,
                            'name'      => 'getProfileContent',
                            'type'      => 'getter',
                            'arguments' => array()
                        )
                    )
                )
            )
        );

        $this->assertEquals($parameters, $method->invoke(new GenerateMultiModelCommand(), $metadata));
    }

    public function testPatternDeclared()
    {
        $refl = new \ReflectionClass('Pok\\Bundle\\DoctrineMultiBundle\\Command\\GenerateMultiModelCommand');
        $method = $refl->getMethod('patternDeclared');
        $method->setAccessible(true);

        $this->assertEquals(
            '`^([a-z]+)(Foo|Bar|FooBar)$`',
            $method->invoke(new GenerateMultiModelCommand(), array('foo', 'bar', 'fooBar'))
        );
    }

    /**
     * @dataProvider getMetadataInfo
     */
    public function testGetElement($namespace, $metadata)
    {
        $refl = new \ReflectionClass('Pok\\Bundle\\DoctrineMultiBundle\\Command\\GenerateMultiModelCommand');
        $method = $refl->getMethod('getElement');
        $method->setAccessible(true);

        $className = $namespace . '\MultiModel\Test';

        try {
            $method->invoke(new GenerateMultiModelCommand(), array(), $className);
        } catch (\RuntimeException $e) {
            $this->assertEquals('Invalid drivers with "' . $className . '".', $e->getMessage());
        }

        $driver = $this->getMockBuilder('Pok\\Bundle\\DoctrineMultiBundle\\Mapping\\Driver\\XmlDriver')->disableOriginalConstructor()->getMock();;
        $driver->expects($this->any())->method('getElement')->will($this->returnValue(simplexml_load_file(__DIR__ . '/../DependencyInjection/Fixtures/Bundles/XmlBundle/Resources/config/doctrine/Test.multi.xml')));
        $driver->expects($this->any())->method('getLocator')->will($this->returnValue(new Locator($namespace . '\MultiModel')));

        $this->assertEquals($metadata, $method->invoke(new GenerateMultiModelCommand(), array($namespace . '\MultiModel' => $driver), $className));
    }

    public function testGetDirectory()
    {
        $refl = new \ReflectionClass('Pok\\Bundle\\DoctrineMultiBundle\\Command\\GenerateMultiModelCommand');
        $method = $refl->getMethod('getDirectory');
        $method->setAccessible(true);

        try {
            $method->invoke(new GenerateMultiModelCommand(), array(), '');
        } catch (\RuntimeException $e) {
            $this->assertEquals('Unknown dir class.', $e->getMessage());
        }

        $this->assertEquals('/path/to/vendor/package/MyBundle/MultiModel/SubModel', $method->invoke(new GenerateMultiModelCommand(), array(
                '/path/to/vendor/package/MyBundle/Resources/config/doctrine' => 'Vendor\\Package\\MyBundle\\MultiModel\\SubModel'
            ),  'Vendor\\Package\\MyBundle\\MultiModel\\SubModel\\Model'
        ));
    }

    public function getMetadataInfo()
    {
        return array(
            array(
                'Pok\Bundle\DoctrineMultiBundle\Tests\DependencyInjection\Fixtures\Bundles\XmlBundle',
                array(
                    'class' => 'Pok\Bundle\DoctrineMultiBundle\Tests\DependencyInjection\Fixtures\Bundles\XmlBundle\MultiModel\Test',
                    'identifier' => array(
                        'manager' => 'entity',
                        'field'   => 'id'
                    ),
                    'models' => array(
                        array(
                            'name'    => 'Pok\Bundle\DoctrineMultiBundle\Tests\DependencyInjection\Fixtures\Bundles\XmlBundle\Entity\Test',
                            'manager' => 'entity',
                            'fields'  => array('name')
                        ),
                        array(
                            'name'    => 'Pok\Bundle\DoctrineMultiBundle\Tests\DependencyInjection\Fixtures\Bundles\XmlBundle\Document\Test',
                            'manager' => 'document',
                            'fields'  => array('profileContent')
                        )
                    ),
                    'dir' => dirname(__DIR__) . '/DependencyInjection/Fixtures/Bundles/XmlBundle/MultiModel'
                )
            )
        );
    }
}

class Locator {
    public function __construct($namespace) {
        $this->namespace = $namespace;
    }

    public function getNamespacePrefixes() {
        return array(dirname(__DIR__) . '/DependencyInjection/Fixtures/Bundles/XmlBundle/Resources/config/doctrine' => $this->namespace);
    }
}
