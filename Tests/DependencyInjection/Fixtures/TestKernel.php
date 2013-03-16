<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class TestKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
 
            new Pok\Bundle\DoctrineMultiBundle\PokDoctrineMultiBundle(),
            new Pok\Bundle\DoctrineMultiBundle\Tests\DependencyInjection\Fixtures\Bundles\XmlBundle\XmlBundle(),
        );

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
