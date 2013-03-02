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
     * @var Mapping\ClassMetadata 
     */
    protected $class;

    /**
     * @param ModelManager          $manager
     * @param Mapping\ClassMetadata $class
     */
    public function __construct(ModelManager $manager, Mapping\ClassMetadata $class)
    {
        $this->manager = $manager;
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
        return $this->manager->find($id);
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
        return $this->manager->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritDoc}
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return $this->manager->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritDoc}
     */
    public function getClassName()
    {
        return $this->class->name;
    }
}
