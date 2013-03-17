<?php

namespace Pok\Bundle\DoctrineMultiBundle\Tests\DependencyInjection\Fixtures\Bundles\XmlBundle\Document;

class Test
{
    private $id;
    private $profileContent;

    public function __construct()
    {
        $this->profileContent = 'Hello world';
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $profileContent
     *
     * @return Test
     */
    public function setProfileContent($profileContent)
    {
        $this->profileContent = (string) $profileContent;

        return $this;
    }

    public function getProfileContent()
    {
        return $this->profileContent;
    }
}
