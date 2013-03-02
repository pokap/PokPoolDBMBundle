<?php

namespace Pok\Bundle\DoctrineMultiBundle\Tests\Mapping;

use Pok\Bundle\DoctrineMultiBundle\Mapping\Driver\XmlDriver;

class XmlMappingDriverTest extends AbstractMappingDriverTest
{
    protected function _loadDriver()
    {
        return new XmlDriver(array(__DIR__ . '/Driver/Fixtures/xml' => 'Pok\Bundle\DoctrineMultiBundle\Tests\Mapping'));
    }
}