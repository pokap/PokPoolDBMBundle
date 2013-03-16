<?php

namespace Pok\Bundle\DoctrineMultiBundle\Tests\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Pok\Bundle\DoctrineMultiBundle\Command\GenerateMultiModelCommand;

class GenerateMultiModelCommandTest extends WebTestCase
{
    public function testExecute()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $application = new Application();
        $application->add(new GenerateMultiModelCommand());

        $command = $application->find('pok:doctrine:multi:generate');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $commandTester->getDisplay();
    }
}
