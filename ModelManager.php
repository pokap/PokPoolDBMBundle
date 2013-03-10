<?php

namespace Pok\Bundle\DoctrineMultiBundle;

use Doctrine\Common\Persistence\ObjectManager;

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
     * @param string               $unitOfWorkClass (optional)
     */
    public function __construct(array $managers, ClassMetadataFactory $metadataFactory = null, $unitOfWorkClass = 'Pok\\Bundle\\DoctrineMultiBundle\\UnitOfWork')
    {
        $this->managers = $managers;

        $this->metadataFactory = $metadataFactory? : new ClassMetadataFactory();
        $this->metadataFactory->setModelManager($this);

        $this->unitOfWork = new $unitOfWorkClass($this);
    }

    /**
     * {@inheritDoc}
     */
    public function getMetadataFactory()
    {
        return $this->metadataFactory;
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
     * Determines whether a document instance is managed in this DocumentManager.
     *
     * @param object $model
     * @return boolean TRUE if this DocumentManager currently manages the given document, FALSE otherwise.
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
            throw ModelException::modelManagerClosed();
        }
    }
}
