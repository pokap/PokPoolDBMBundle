<?php

namespace Pok\Bundle\PoolDBMBundle;

use Symfony\Bundle\FrameworkBundle\Console\Application as FrameworkConsoleApplication;
use Symfony\Component\Console\Application;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use Pok\PoolDBM\Console\ConsoleRunner;

class PokPoolDBMBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function registerCommands(Application $application)
    {
        if (!$application instanceof FrameworkConsoleApplication) {
            return;
        }

//        parent::registerCommands($application);

        $application->setHelperSet(ConsoleRunner::createHelperSet(
            $this->container->get('pok.pool_dbm.manager'),
            null,
            $application->getKernel()->getCacheDir()
        ));
        ConsoleRunner::addDefaultCommands($application);
    }
}
