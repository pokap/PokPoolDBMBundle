<?php

namespace MultiModel;

class User
{
    private $entity;
    private $document;

    private $address;

    public function __construct()
    {
        $this->entity = new \Entity\User();
        $this->document = new \Document\User();
    }

    public function getId()
    {
        return $this->document->getId();
    }

    public function getName()
    {
        return $this->entity->getName();
    }

    public function setName($name)
    {
        $this->entity->setName($name);

        return $this;
    }

    public function getProfileContent()
    {
        return $this->document->getProfileContent();
    }

    public function setProfileContent($profileContent)
    {
        $this->document->setProfileContent($profileContent);

        return $this;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setAddress(Address $address)
    {
        $this->address = $address;

        return $this;
    }
}
