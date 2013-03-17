<?php

namespace Pok\Bundle\DoctrineMultiBundle\Mapping;

use Doctrine\Common\Persistence\Mapping\AbstractClassMetadataFactory;
use Doctrine\Common\Persistence\Mapping\ClassMetadata as ClassMetadataInterface;
use Doctrine\Common\Persistence\Mapping\ReflectionService;

use Pok\Bundle\DoctrineMultiBundle\Mapping\ClassMetadata;
use Pok\Bundle\DoctrineMultiBundle\Mapping\MappingException;
use Pok\Bundle\DoctrineMultiBundle\ModelManager;

class ClassMetadataFactory extends AbstractClassMetadataFactory
{
    protected $cacheSalt = "\$POKDOCTRINEMULTICLASSMETADATA";

    /** @var ModelManager */
    private $manager;

    /** @var \Doctrine\Common\Persistence\Mapping\Driver\MappingDriver The used metadata driver. */
    private $driver;

    private $namespace;

    /**
     * @param ModelManager $manager
     */
    public function setManager(ModelManager $manager)
    {
        $this->manager = $manager;
    }

    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * Lazy initialization of this stuff, especially the metadata driver,
     * since these are not needed at all when a metadata cache is active.
     */
    protected function initialize()
    {
        $this->driver = $this->manager->getMetadataDriverImpl();
        $this->initialized = true;
    }

    /**
     * {@inheritDoc}
     */
    protected function getFqcnFromAlias($namespaceAlias, $simpleClassName)
    {
        return $this->namespace . '\\' . $simpleClassName;
    }

    /**
     * {@inheritDoc}
     */
    protected function getDriver()
    {
        return $this->driver;
    }

    /**
     * {@inheritDoc}
     */
    protected function wakeupReflection(ClassMetadataInterface $class, ReflectionService $reflService)
    {
    }

    /**
     * {@inheritDoc}
     */
    protected function initializeReflection(ClassMetadataInterface $class, ReflectionService $reflService)
    {
    }

    /**
     * {@inheritDoc}
     */
    protected function isEntity(ClassMetadataInterface $class)
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    protected function doLoadMetadata($class, $parent, $rootEntityFound, array $nonSuperclassParents = array())
    {
        /** @var $class ClassMetadata */

        // Invoke driver
        try {
            $this->driver->loadMetadataForClass($class->getName(), $class);
        } catch (\ReflectionException $e) {
            throw MappingException::reflectionFailure($class->getName(), $e);
        }

        $this->validateIdentifier($class);
    }

    /**
     * Validates the identifier mapping.
     *
     * @param ClassMetadata $class
     */
    protected function validateIdentifier($class)
    {
        if (!$class->identifier) {
            throw MappingException::identifierRequired($class->name);
        }
    }

    /**
     * @param string $className
     */
    protected function newClassMetadataInstance($className)
    {
        return new ClassMetadata($className);
    }
}
