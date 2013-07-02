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

        $application_helper = $application->getHelperSet();

        $helpers = ConsoleRunner::createHelpers(
            $this->container->get('pok.pool_dbm.manager'),
            null,
            $application->getKernel()->getCacheDir()
        );

        foreach ($helpers as $name => $helper) {
            if ($application_helper->has($name)) {
                continue;
            }

            $application_helper->set($helper);
        }

        ConsoleRunner::addDefaultCommands($application);
    }
}
