<?php

namespace Pok\Bundle\DoctrineMultiBundle;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;

use Pok\Bundle\DoctrineMultiBundle\Mapping\ClassMetadataFactory;

class ModelManager implements ObjectManager
{
    /**
     * The metadata factory, used to retrieve the ODM metadata of document classes.
     *
     * @var ClassMetadataFactory
     */
    private $metadataFactory;

    /**
     * @var MappingDriverChain 
     */
    private $metadataDriverImpl;

    /**
     * The ModelRepository instances.
     *
     * @var array
     */
    private $repositories = array();

    /**
     * @var array
     */
    private $managers = array();

    /**
     * The UnitOfWork used to coordinate object-level transactions.
     *
     * @var UnitOfWork
     */
    private $unitOfWork;

    /**
     * Whether the ModelManager is closed or not.
     *
     * @var bool
     */
    private $closed = false;

    /**
     * Constructor.
     *
     * @param array                $managers
     * @param ClassMetadataFactory $metadataFactory (optional)
     */
    public function __construct(array $managers, ClassMetadataFactory $metadataFactory = null)
    {
        $this->managers = $managers;

        $this->metadataFactory = $metadataFactory? : new ClassMetadataFactory();
        $this->metadataFactory->setManager($this);

        $this->unitOfWork = new UnitOfWork($this);
    }

    /**
     * {@inheritDoc}
     */
    public function getMetadataFactory()
    {
        return $this->metadataFactory;
    }

    /**
     * @param MappingDriverChain $metadataDriver
     */
    public function setMetadataDriverImpl(MappingDriverChain $driverChain)
    {
        $this->metadataDriverImpl = $driverChain;
    }

    /**
     * @return MappingDriverChain
     */
    public function getMetadataDriverImpl()
    {
        return $this->metadataDriverImpl;
    }

    /**
     * Sets manager instance.
     *
     * @param string $name
     * @param object $instance
     */
    public function setManager($name, $instance)
    {
        $this->managers[$name] = $instance;
    }

    /**
     * @return array
     */
    public function getManagers()
    {
        return $this->managers;
    }

    /**
     * {@inheritDoc}
     */
    public function initializeObject($obj)
    {
    }

    /**
     * @return UnitOfWork
     */
    public function getUnitOfWork()
    {
        return $this->unitOfWork;
    }

    /**
     * {@inheritDoc}
     *
     * @return Mapping\ClassMetadata
     */
    public function getClassMetadata($className)
    {
        return $this->metadataFactory->getMetadataFor($className);
    }

    /**
     * @param string $modelName The model class name
     *
     * @return Query\Builder
     */
    public function createQueryBuilder($className, $alias)
    {
        $class = $this->getClassMetadata($className);
        $repository = $this->managers[$class->identifier['manager']]->getRepository($class->fieldMappings[$class->identifier['manager']]['modelName']);

        if (!method_exists($repository, 'createQueryBuilder')) {
            throw new \BadMethodCallException(sprintf('The repository of manager "%s" not implement createQueryBuilder.', $class->identifier['manager']));
        }

        return $repository->createQueryBuilder($alias);
    }

    /**
     * {@inheritDoc}
     */
    public function persist($model)
    {
        if (!is_object($model)) {
            throw new \InvalidArgumentException(gettype($model));
        }

        $this->errorIfClosed();
        $this->unitOfWork->persist($model);
    }

    /**
     * @param object $model The document instance to remove.
     *
     * @throws \InvalidArgumentException When model is not an object
     */
    public function remove($model)
    {
        if (!is_object($model)) {
            throw new \InvalidArgumentException(gettype($model));
        }
        $this->errorIfClosed();
        $this->unitOfWork->remove($model);
    }

    /**
     * @param object $model The document to refresh.
     *
     * @throws \InvalidArgumentException When model is not an object
     */
    public function refresh($model)
    {
        if (!is_object($model)) {
            throw new \InvalidArgumentException(gettype($model));
        }
        $this->errorIfClosed();
        $this->unitOfWork->refresh($model);
    }

    /**
     * @param object $model The document to detach.
     *
     * @throws \InvalidArgumentException When model is not an object
     */
    public function detach($model)
    {
        if (!is_object($model)) {
            throw new \InvalidArgumentException(gettype($model));
        }
        $this->unitOfWork->detach($model);
    }

    /**
     * @param object $model The detached document to merge into the persistence context.
     * @return object The managed copy of the document.
     *
     * @throws \InvalidArgumentException When model is not an object
     */
    public function merge($model)
    {
        if (!is_object($model)) {
            throw new \InvalidArgumentException(gettype($model));
        }
        $this->errorIfClosed();
        return $this->unitOfWork->merge($model);
    }

    /**
     * @param string $modelName  The name of the Model.
     * @return DocumentRepository  The repository.
     */
    public function getRepository($modelName)
    {
        if (isset($this->repositories[$modelName])) {
            return $this->repositories[$modelName];
        }

        $metadata = $this->getClassMetadata($modelName);
        $customRepositoryClassName = $metadata->customRepositoryClassName;

        if ($customRepositoryClassName !== null) {
            $repository = new $customRepositoryClassName($this, $this->unitOfWork, $metadata);
        } else {
            $repository = new ModelRepository($this, $this->unitOfWork, $metadata);
        }

        $this->repositories[$modelName] = $repository;

        return $repository;
    }

    /**
     * @param object $model
     * @param array $options
     *
     * @throws \InvalidArgumentException When model is not an object
     */
    public function flush($model = null, array $options = array())
    {
        if (null !== $model && !is_object($model) && !is_array($model)) {
            throw new \InvalidArgumentException(gettype($model));
        }
        $this->errorIfClosed();
        $this->unitOfWork->commit($model, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function find($className, $id)
    {
        return $this->getRepository($className)->find($id);
    }

    /**
     * Clears the managers of ModelManager. All models that are currently managed in this manager become detached.
     *
     * @param string|null $modelName
     */
    public function clear($modelName = null)
    {
        $this->unitOfWork->clear($modelName);
    }

    public function close()
    {
        $this->clear();
        $this->closed = true;
    }

    /**
     * Determines whether a model instance is managed in this ModelManager.
     *
     * @param object $model
     *
     * @return boolean TRUE if this ModelManager currently manages the given document, FALSE otherwise.
     *
     * @throws \InvalidArgumentException When model is not an object
     */
    public function contains($model)
    {
        if (!is_object($model)) {
            throw new \InvalidArgumentException(gettype($model));
        }

        $class = $this->getClassMetadata(get_class($model));

        foreach ($class->reflFields as $manager => $reflClass) {
            if (!$this->managers[$manager]->contains($reflClass->getValue($model))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Throws an exception if the ModelManager is closed or currently not active.
     *
     * @throws ModelException If the ModelManager is closed.
     */
    private function errorIfClosed()
    {
        if ($this->closed) {
            throw new \RuntimeException('Model manager is closed.');
        }
    }
}
