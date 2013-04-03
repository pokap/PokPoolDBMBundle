<?php

namespace MultiModel;

use Doctrine\Common\Collections\ArrayCollection;

class Address
{
    private $entity;
    private $document;

    private $users;

    public function __construct()
    {
        $this->entity = new \Entity\Address();
        $this->document = new \Document\Address();

        $this->users = new ArrayCollection();
    }

    public function getId()
    {
        return $this->entity->getId();
    }

    public function setCity($city)
    {
        $this->document->setCity($city);

        return $this;
    }

    public function getCity()
    {
        return $this->document->getCity();
    }

    public function getUsers()
    {
        return $this->users;
    }
}