<?php

namespace Pok\Bundle\PoolDBMBundle\Tests\DependencyInjection\Fixtures\Bundles\XmlBundle\DependencyInjection;

use Doctrine\Common\Persistence\ObjectManager;

class FakeService implements ObjectManager
{
    public function getRepository($className) {}
    public function persist($object) {}
    public function remove($object) {}
    public function flush($object = null) {}
    public function clear($object = null) { }
    public function find($className, $id) {}
    public function merge($object) {}
    public function detach($object) {}
    public function refresh($object) {}
    public function getClassMetadata($className) {}
    public function getMetadataFactory() {}
    public function initializeObject($obj) {}
    public function contains($object) {}
}
