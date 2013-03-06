<?php

namespace Pok\Bundle\DoctrineMultiBundle\Mapping;

use Doctrine\Common\Persistence\Mapping\MappingException as BaseMappingException;

class MappingException extends BaseMappingException
{
    public static function mappingNotFound($className, $field)
    {
        return new self("No mapping found for field '$field' in class '$className'.");
    }

    public static function duplicateFieldMapping($model, $field)
    {
        return new self(sprintf('Property "%s" in "%s" was already declared, but it must be declared only once', $field, $model));
    }

    public static function missingField($className)
    {
        return new self("The MultiModel class '$className' field mapping misses the 'field' attribute.");
    }

    public static function reflectionFailure($model, \ReflectionException $previousException)
    {
        return new self('An error occurred in ' . $model, 0, $previousException);
    }

    public static function missingIdReferenceClass($className)
    {
        return new self("The class-option for the custom ID reference is missing in class $className.");
    }
}
