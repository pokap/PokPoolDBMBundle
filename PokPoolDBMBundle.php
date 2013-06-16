<?php

namespace Pok\Bundle\PoolDBMBundle;

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
//        parent::registerCommands($application);

        ConsoleRunner::addDefaultCommands($application);
    }
}
