<?php

namespace Pok\Bundle\PoolDBMBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Bridge\Doctrine\DependencyInjection\AbstractDoctrineExtension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class PokPoolDBMExtension extends AbstractDoctrineExtension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('multi.xml');

        // reset state of drivers and alias map. They are only used by this methods and children.
        $this->drivers = array();
        $this->aliasMap = array();
        $config['name'] = 'default';

        $this->loadMappingInformation($config, $container);
        $this->registerMappingDrivers($config, $container);
        $this->configureManagers($config, $container);
    }

    /**
     * @return string
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__.'/../Resources/config/schema';
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    protected function configureManagers(array $config, ContainerBuilder $container)
    {
        $managers = array();
        foreach ($config['managers'] as $name => $info) {
            $managers[$name] = $this->getObjectManagerElementName(sprintf('manager.%s', $name));

            $container->setAlias($managers[$name], $info['id']);
        }

        $container->setParameter($this->getObjectManagerElementName('managers'), $managers);

        $manager = $container->getDefinition('pok.pool_dbm.manager.pool');
        foreach ($managers as $name => $service) {
            $manager->addMethodCall('addManager', array($name, new Reference($service)));
        }

        $manager = $container->getDefinition('pok.pool_dbm.manager');
        $manager->addMethodCall('setMetadataDriverImpl', array(new Reference(sprintf('pok.pool_dbm.%s_metadata_driver', $config['name']))));
    }

    /**
     * {@inheritDoc}
     */
    protected function getMappingObjectDefaultName()
    {
        return 'MultiModel';
    }

    /**
     * {@inheritDoc}
     */
    protected function getMappingResourceConfigDirectory()
    {
        return 'Resources/config/doctrine';
    }

    /**
     * {@inheritDoc}
     */
    protected function getMappingResourceExtension()
    {
        return 'multi';
    }

    /**
     * {@inheritDoc}
     */
    protected function getObjectManagerElementName($name)
    {
        return 'pok.pool_dbm.' . $name;
    }
}
