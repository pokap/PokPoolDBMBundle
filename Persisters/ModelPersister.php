<?php

namespace Pok\Bundle\DoctrineMultiBundle\Persisters;

use Pok\Bundle\DoctrineMultiBundle\ModelManager;
use Pok\Bundle\DoctrineMultiBundle\UnitOfWork;
use Pok\Bundle\DoctrineMultiBundle\ClassMetadata;

class ModelPersister
{
    /**
     * The DocumentManager instance.
     *
     * @var ModelManager
     */
    private $manager;

    /**
     * The UnitOfWork instance.
     *
     * @va UnitOfWork
     */
    private $uow;

    /**
     * The ClassMetadata instance for the document type being persisted.
     *
     * @var ClassMetadata
     */
    private $class;

    /**
     * Array of queued inserts for the persister to insert.
     *
     * @var array
     */
    private $queuedInserts = array();

    /**
     * Constructor.
     */
    public function __construct(ModelManager $manager, UnitOfWork $uow, ClassMetadata $class)
    {
        $this->manager = $manager;
        $this->uow = $uow;
        $this->class = $class;
    }

    public function getInserts()
    {
        return $this->queuedInserts;
    }

    public function isQueuedForInsert($model)
    {
        return isset($this->queuedInserts[spl_object_hash($model)]);
    }

    /**
     * Adds a document to the queued insertions.
     * The document remains queued until {@link executeInserts} is invoked.
     *
     * @param object $model The document to queue for insertion.
     */
    public function addInsert($model)
    {
        $this->queuedInserts[spl_object_hash($model)] = $model;
    }

    /**
     * Gets the ClassMetadata instance of the document class this persister is used for.
     *
     * @return ClassMetadata
     */
    public function getClassMetadata()
    {
        return $this->class;
    }

    /**
     * Loads an document by a list of field criteria.
     *
     * @param array $criteria The criteria by which to load the document.
     *
     * @return object The loaded and managed document instance or NULL if the document can not be found.
     */
    public function load($criteria)
    {
        $managers = $this->manager->getManagers();
        $identifier = $this->class->getIdentifier();

        $models = array();
        foreach ($this->class->fieldMappings as $manager => $reflClass) {
            $models[$manager] = $reflClass['modelName'];
        }

        $result = array();
        $result[$identifier['manager']] = $managers[$identifier['manager']]->getRepository($models[$identifier['manager']])->findOneBy($criteria);
        unset($models[$identifier['manager']]);

        $id = $this->class->reflIdFields[$identifier['manager']]->getValue($result[$identifier['manager']]);

        foreach ($models as $manager => $model) {
            $result[$manager] = $managers[$manager]->getRepository($model)->find($id);
        }

        return $this->createModel($result);
    }

    /**
     * Loads a list of model by a list of field criteria.
     *
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit (optional)
     * @param integer $offset (optional)
     *
     * @return array
     */
    public function loadAll(array $criteria = array(), array $orderBy = null, $limit = null, $offset = null)
    {
        $managers = $this->manager->getManagers();
        $identifier = $this->class->getIdentifier();

        $models = array();
        foreach ($this->class->fieldMappings as $manager => $mapping) {
            $models[$manager] = $mapping['modelName'];
        }

        $data = array();
        $ids = array();
        foreach ($managers[$identifier['manager']]->getRepository($models[$identifier['manager']])->findBy($criteria, $orderBy, $limit, $offset) as $object) {
            $id = $this->class->reflIdFields[$identifier['manager']]->getValue($object);

            $data[$id][$identifier['manager']] = $object;
            $ids[] = $id;
        }

        unset($models[$identifier['manager']]);

        foreach ($models as $manager => $model) {
            $reflId = $this->class->reflIdFields[$identifier['manager']];

            foreach ($managers[$manager]->getRepository($model)->findBy($ids) as $object) {
                $id = $reflId->getValue($object);

                $data[$id][$manager] = $object;
            }
        }

        $result = array();
        foreach ($ids as $id) {
            $result[] = $this->createModel($data[$id]);
        }

        return $result;
    }

    /**
     * @param array $data
     * @return object
     */
    private function createModel(array $data)
    {
        if (empty($data)) {
            return null;
        }

        return $this->uow->createModel($this->class->name, $data);
    }
}
