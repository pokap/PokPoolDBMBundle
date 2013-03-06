<?php

namespace Pok\Bundle\DoctrineMultiBundle\Mapping;

class ClassMetadata extends ClassMetadataInfo
{
    /**
     * The prototype from which new instances of the mapped class are created.
     *
     * @var object
     */
    private $prototype;

    /**
     * Constructor.
     *
     * @param string $modelName
     */
    public function __construct($modelName)
    {
        parent::__construct($modelName);

        $this->reflClass = new \ReflectionClass($modelName);
    }

    /**
     * {@inheritDoc}
     */
    public function addModel($field, $modelName, array $subFields)
    {
        if (!$this->reflClass->hasProperty($field)) {
            return;
        }

        $this->reflFields[$field] = self::retrieveReflAttr($this->reflClass, $field);

        parent::addModel($field, $modelName, $subFields);
    }

    /**
     * {@inheritDoc}
     */
    public function setIdentifier($manager, $field)
    {
        if (empty($this->fieldMappings)) {
            throw new \RuntimeException('ClassMetadata::setIdentifier must be call after addModel.');
        }

        foreach ($this->fieldMappings as $model) {
            $this->reflIdFields[$model['manager']] = self::retrieveReflAttr(new \ReflectionClass($model['modelName']), $field);
        }

        parent::setIdentifier($manager, $field);
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        // This metadata is always serialized/cached.
        $serialized = array(
            'fieldMappings',
            'identifier',
            'name',
        );

        if ($this->customRepositoryClassName) {
            $serialized[] = 'customRepositoryClassName';
        }

        return $serialized;
    }

    /**
     * Restores some state that can not be serialized/unserialized.
     *
     * @return void
     */
    public function __wakeup()
    {
        // Restore ReflectionClass and properties
        $this->reflClass = new \ReflectionClass($this->name);

        foreach ($this->fieldMappings as $modelName => $model) {
            if (!isset($this->reflFields[$model['manager']])) {
                $this->reflFields[$model['manager']] = self::retrieveReflAttr($this->reflClass, $model['manager']);
            }

            // identifier
            $this->reflIdFields[$model['manager']] = self::retrieveReflAttr(new \ReflectionClass($modelName), $this->identifier['field']);
        }
    }

    /**
     * @param \ReflectionClass $reflClass
     * @param string           $attr
     *
     * @return \ReflectionProperty
     */
    private static function retrieveReflAttr(\ReflectionClass $reflClass, $attr)
    {
        $relfField = $reflClass->getProperty($attr);
        $relfField->setAccessible(true);

        return $relfField;
    }

    /**
     * Creates a new instance of the mapped class, without invoking the constructor.
     *
     * @return object
     */
    public function newInstance()
    {
        if ($this->prototype === null) {
            $this->prototype = unserialize(sprintf('O:%d:"%s":0:{}', strlen($this->name), $this->name));
        }

        return clone $this->prototype;
    }
}
