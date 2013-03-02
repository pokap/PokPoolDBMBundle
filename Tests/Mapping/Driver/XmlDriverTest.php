<?php

namespace Pok\Bundle\DoctrineMultiBundle\Tests\Mapping\Driver;

use Pok\Bundle\DoctrineMultiBundle\Mapping\Driver\XmlDriver;

class XmlDriverTest extends AbstractDriverTest
{
    protected function getFileExtension()
    {
        return '.multi.xml';
    }

    protected function getFixtureDir()
    {
        return __DIR__ . '/Fixtures/xml';
    }

    protected function getDriver(array $prefixes = array())
    {
        return new XmlDriver($prefixes, $this->getFileExtension());
    }
}
