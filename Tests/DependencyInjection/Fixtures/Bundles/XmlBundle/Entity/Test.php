<?php

namespace Pok\Bundle\DoctrineMultiBundle\Tests\DependencyInjection\Fixtures\Bundles\XmlBundle\Entity;

class Test
{
    private $id;
    private $name;

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = (string) $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }
}
