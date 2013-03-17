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
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $filesystem = new Filesystem();
        $this->assertTrue($filesystem->exists('/home/vagrant/apps/pokap/DoctrineMultiBundle/Tests/DependencyInjection/Fixtures/Bundles/XmlBundle/MultiModel/Test.php'));
    }
}
