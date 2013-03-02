<?php

namespace Pok\Bundle\DoctrineMultiBundle;

use Doctrine\Common\PropertyChangedListener;

class UnitOfWork implements PropertyChangedListener
{
    /**
     * @var ModelManager
     */
    private $manager;

    /**
     * Constructor.
     *
     * @param ModelManager $manager
     */
    public function __construct(ModelManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param object $model
     * @param array $options
     */
    public function flush($model = null, array $options = array())
    {
        $class = $this->manager->getClassMetadata(get_class($model));
        $managers = $this->manager->getManagers();

        foreach (array_keys($class->fieldMappings) as $managerName) {
            $managers[$managerName]->commit($class->reflFields[$managerName]->getValue($model), $options);
        }
    }

    /**
     * @param object $model
     */
    public function persist($model)
    {
        $class = $this->manager->getClassMetadata(get_class($model));
        $managers = $this->manager->getManagers();

        foreach (array_keys($class->fieldMappings) as $managerName) {
            $managers[$managerName]->persist($class->reflFields[$managerName]->getValue($model));
        }
    }

    /**
     * @param object $model
     */
    public function remove($model)
    {
        $class = $this->manager->getClassMetadata(get_class($model));
        $managers = $this->manager->getManagers();

        foreach (array_keys($class->fieldMappings) as $managerName) {
            $managers[$managerName]->remove($class->reflFields[$managerName]->getValue($model));
        }
    }

    /**
     * @param object $model
     *
     * @return object
     */
    public function merge($model)
    {
        $class = $this->manager->getClassMetadata(get_class($model));
        $managers = $this->manager->getManagers();

        foreach (array_keys($class->fieldMappings) as $managerName) {
            $managers[$managerName]->merge($class->reflFields[$managerName]->getValue($model));
        }
    }

    /**
     * @param object $model The model to detach.
     */
    public function detach($model)
    {
        $class = $this->manager->getClassMetadata(get_class($model));
        $managers = $this->manager->getManagers();

        foreach (array_keys($class->fieldMappings) as $managerName) {
            $managers[$managerName]->detach($class->reflFields[$managerName]->getValue($model));
        }
    }

    /**
     * @param object $model
     */
    public function refresh($model)
    {
        $class = $this->manager->getClassMetadata(get_class($model));
        $managers = $this->manager->getManagers();

        foreach (array_keys($class->fieldMappings) as $managerName) {
            $managers[$managerName]->refresh($class->reflFields[$managerName]->getValue($model));
        }
    }

    /**
     * @param string|null $modelName (optional)
     */
    public function clear($modelName = null)
    {
        $class = $this->manager->getClassMetadata(get_class($model));
        $managers = $this->manager->getManagers();

        foreach (array_keys($class->fieldMappings) as $managerName) {
            $managers[$managerName]->clear($modelName? $class->reflFields[$managerName]->getValue($model) : null);
        }
    }

    /**
     * @param string $className
     * @param array $data
     *
     * @return object The model instance.
     */
    public function createModel($className, $data)
    {
        $class = $this->manager->getClassMetadata($className);

        $model = $class->newInstance();

        foreach ($data as $field => $value) {
            if (isset($class->fieldMappings[$field])) {
                $class->setFieldValue($model, $field, $value);
            }
        }

        return $model;
    }

    /**
     * {@inheritDoc}
     */
    public function propertyChanged($sender, $propertyName, $oldValue, $newValue)
    {
        throw new \LogicException('Use directely model to change property.');
    }
}
