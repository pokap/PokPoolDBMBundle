<?php

namespace Pok\Bundle\DoctrineMultiBundle;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;

class ModelRepository implements ObjectRepository
{
    /**
     * @var ModelManager
     */
    protected $manager;

    /**
     * @var UnitOfWork
     */
    protected $uow;

    /**
     * @var Mapping\ClassMetadata 
     */
    protected $class;

    /**
     * Construtor.
     *
     * @param ModelManager          $manager
     * @param UnitOfWork            $uow
     * @param Mapping\ClassMetadata $class
     */
    public function __construct(ModelManager $manager, UnitOfWork $uow, Mapping\ClassMetadata $class)
    {
        $this->manager = $manager;
        $this->uow     = $uow;
        $this->class   = $class;
    }

    public function createQueryBuilder($alias)
    {
        return $this->manager->createQueryBuilder($this->class->name, $alias);
    }

    /**
     * {@inheritDoc}
     */
    public function find($id)
    {
        return $this->uow->getMultiModelPersister($this->class->name)->load($id);
    }

    /**
     * {@inheritDoc}
     */
    public function findAll()
    {
        return $this->findBy(array());
    }

    /**
     * {@inheritDoc}
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->uow->getMultiModelPersister($this->class->name)->loadAll($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritDoc}
     */
    public function findOneBy(array $criteria)
    {
        return $this->find($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function getClassName()
    {
        return $this->class->name;
    }
}
