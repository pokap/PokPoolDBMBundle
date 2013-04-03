<?php

namespace Pok\Bundle\DoctrineMultiBundle\Mapping;

class ClassMetadataInfo implements \Doctrine\Common\Persistence\Mapping\ClassMetadata
{
    /**
     * READ-ONLY: The name of the model class.
     */
    public $name;

    /**
     * @var string
     */
    public $customRepositoryClassName;

    /**
     * @var array
     */
    public $identifier = array();

    /**
     * @var array
     */
    public $fieldMappings = array();

    /**
     * The ReflectionClass instance of the mapped class.
     *
     * @var \ReflectionClass
     */
    public $reflClass;

    /**
     * @var \ReflectionProperty[]
     */
    public $reflFields = array();

    /**
     * @var \ReflectionProperty[]
     */
    public $reflIdFields = array();

    /**
     * Constructor.
     *
     * @param string $modelName
     */
    public function __construct($modelName)
    {
        $this->name = $modelName;
    }

    /**
     * {@inheritDoc}
     */
    public function getReflectionClass()
    {
        if (!$this->reflClass) {
            $this->reflClass = new \ReflectionClass($this->name);
        }

        return $this->reflClass;
    }

    /**
     * {@inheritDoc}
     */
    public function isIdentifier($fieldName)
    {
        return $this->identifier['field'] === $fieldName;
    }

    /**
     * @param string $manager
     * @param string $field
     */
    public function setIdentifier($manager, $field)
    {
        $this->identifier = array(
            'manager' => $manager,
            'field'   => $field
        );
    }

    /**
     * @return array
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns configuration mapping value by manager name.
     *
     * @param string $manager
     * @param string $key
     * @param mixed  $default (optional)
     *
     * @return mixed
     */
    public function getFieldMappingValue($manager, $key, $default = null)
    {
        if (!isset($this->fieldMappings[$manager][$key])) {
            return $default;
        }

        return $this->fieldMappings[$manager][$key];
    }

    /**
     * Get identifier field names of this class.
     *
     * @return array
     */
    public function getIdentifierFieldNames()
    {
        return array($this->identifier);
    }

    /**
     * Checks whether the class has a (mapped) field with a certain name.
     *
     * @return boolean
     */
    public function hasField($fieldName)
    {
        return isset($this->fieldMappings[$fieldName]);
    }

    /**
     * Registers a custom repository class for the model class.
     *
     * @param string $mapperClassName  The class name of the custom mapper.
     */
    public function setCustomRepositoryClass($repositoryClassName)
    {
        $this->customRepositoryClassName = $repositoryClassName;
    }

    /**
     * Gets the ReflectionProperties of the mapped class.
     *
     * @return array An array of ReflectionProperty instances.
     */
    public function getReflectionProperties()
    {
        return $this->reflFields;
    }

    /**
     * Gets a ReflectionProperty for a specific field of the mapped class.
     *
     * @param string $name
     * @return ReflectionProperty
     */
    public function getReflectionProperty($name)
    {
        return $this->reflFields[$name];
    }

    /**
     * The name of this Model class.
     *
     * @return string $name The MultiModel class name.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Map fields per model.
     */
    public function addModel($field, $modelName, array $subFields, $repository_method)
    {
        $mapping = array(
            'modelName'         => $modelName,
            'manager'           => $field,
            'fields'            => $subFields,
            'repository-method' => $repository_method
        );

        $mapping['fieldName'] =& $mapping['manager'];
        $mapping['name']      =& $mapping['manager'];

        $this->fieldMappings[$field] = $mapping;

        return $mapping;
    }

    /**
     * Sets the model identifier of a model.
     *
     * @param object $model
     * @param mixed $id
     */
    public function setIdentifierValue($model, $id)
    {
        foreach ($this->reflIdFields as $object) {
            $object->setValue($model, $id);
        }
    }

    /**
     * Gets the model identifier.
     *
     * @param object $model
     * @return string $id
     */
    public function getIdentifierValue($model)
    {
        return $this->reflIdFields[$this->identifier['manager']]->getValue($model);
    }

    /**
     * @param object $model
     * @return array
     */
    public function getIdentifierValues($model)
    {
        return array($this->identifier['field'] => $this->getIdentifierValue($model));
    }

    /**
     * Sets the specified field to the specified value on the given multi-model.
     *
     * @param object $model
     * @param string $manager
     * @param mixed $value
     */
    public function setFieldValue($model, $manager, $value)
    {
        $this->reflFields[$manager]->setValue($model, $value);
    }

    /**
     * Gets the specified field's value off the given model.
     *
     * @param string $manager
     * @param object $model
     */
    public function getFieldValue($model, $manager)
    {
        return $this->reflFields[$manager]->getValue($model);
    }

    /**
     * Gets the mapping of a field.
     *
     * @param string $fieldName  The field name.
     * @return array  The field mapping.
     */
    public function getFieldMapping($fieldName)
    {
        if (!isset($this->fieldMappings[$fieldName])) {
            throw MappingException::mappingNotFound($this->name, $fieldName);
        }

        return $this->fieldMappings[$fieldName];
    }

    /**
     * @return array
     */
    public function getFieldNames()
    {
        $fields = array();
        foreach ($this->fieldMappings as $mapping) {
            $fields = array_merge($fields, $mapping['fields']);
        }

        return $fields;
    }

    /**
     * @return array
     */
    public function getFieldManagerNames()
    {
        return array_keys($this->fieldMappings);
    }

    public function getAssociationMappedByTargetField($assocName)
    {
        throw new \RuntimeException('Association mapped has not implemented.');
    }

    public function getAssociationNames()
    {
        throw new \RuntimeException('Association mapped has not implemented.');
    }

    public function getAssociationTargetClass($assocName)
    {
        throw new \RuntimeException('Association mapped has not implemented.');
    }

    public function getTypeOfField($fieldName)
    {
        throw new \LogicException('There are not have type file notion.');
    }

    public function hasAssociation($fieldName)
    {
        throw new \RuntimeException('Association mapped has not implemented.');
    }

    public function isAssociationInverseSide($assocName)
    {
        throw new \RuntimeException('Association mapped has not implemented.');
    }

    public function isCollectionValuedAssociation($fieldName)
    {
        throw new \RuntimeException('Association mapped has not implemented.');
    }

    public function isSingleValuedAssociation($fieldName)
    {
        throw new \RuntimeException('Association mapped has not implemented.');
    }
}
