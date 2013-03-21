<?php

namespace Pok\Bundle\DoctrineMultiBundle\Persisters;

use Pok\Bundle\DoctrineMultiBundle\ModelManager;
use Pok\Bundle\DoctrineMultiBundle\UnitOfWork;
use Pok\Bundle\DoctrineMultiBundle\Mapping\ClassMetadata;

class ModelPersister
{
    /**
     * The ModelManager instance.
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
     * The ClassMetadata instance for the multi-model type being persisted.
     *
     * @var ClassMetadata
     */
    private $class;

    /**
     * Constructor.
     */
    public function __construct(ModelManager $manager, UnitOfWork $uow, ClassMetadata $class)
    {
        $this->manager = $manager;
        $this->uow = $uow;
        $this->class = $class;
    }

    /**
     * Gets the ClassMetadata instance of the multi-model class this persister is used for.
     *
     * @return ClassMetadata
     */
    public function getClassMetadata()
    {
        return $this->class;
    }

    /**
     * Loads an multi-model by a list of field criteria.
     *
     * @param mixed $id
     *
     * @return object The loaded and managed multi-model instance or NULL if the multi-model can not be found
     */
    public function load($id)
    {
        $managers = $this->manager->getManagers();
        $identifier = $this->class->getIdentifier();

        $models = array();
        foreach ($this->class->fieldMappings as $manager => $reflClass) {
            $models[$manager] = $reflClass['modelName'];
        }

        $result = array();
        $result[$identifier['manager']] = $managers[$identifier['manager']]->getRepository($models[$identifier['manager']])->find($id);
        unset($models[$identifier['manager']]);

        $id = $this->class->reflIdFields[$identifier['manager']]->getValue($result[$identifier['manager']]);

        foreach ($models as $manager => $model) {
            $result[$manager] = $managers[$manager]->getRepository($model)->find($id);
        }

        return $this->createMultiModel($result);
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

            foreach ($managers[$manager]->getRepository($model)->findBy(array($identifier['field'] => $ids)) as $object) {
                $id = $reflId->getValue($object);

                $data[$id][$manager] = $object;
            }
        }

        $result = array();
        foreach ($ids as $id) {
            $result[] = $this->createMultiModel($data[$id]);
        }

        return $result;
    }

    /**
     * @param array $data
     * @return object
     */
    private function createMultiModel(array $data)
    {
        if (empty($data)) {
            return null;
        }

        return $this->uow->createMultiModel($this->class->name, $data);
    }
}
