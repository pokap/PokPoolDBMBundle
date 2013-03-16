<?php

namespace Pok\Bundle\DoctrineMultiBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateMultiModelCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('pok:doctrine:multi:generate')
            ->setDescription('Generate multi-model')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $templating = $this->getContainer()->get('templating');

        var_dump($this->getContainer()->get('pok.doctrine_multi.manager'));
    }
}
