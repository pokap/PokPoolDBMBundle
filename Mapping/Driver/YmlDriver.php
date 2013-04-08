<?php

namespace Pok\Bundle\PoolDBMBundle\Mapping\Driver;

use Doctrine\Common\Persistence\Mapping\Driver\SymfonyFileLocator;
use Pok\PoolDBM\Mapping\Driver\YmlDriver as BaseYmlDriver;

/**
 * XmlDriver.
 *
 * @author Florent Denis <dflorent.pokap@gmail.com>
 */
class YmlDriver extends BaseYmlDriver
{
    /**
     * {@inheritDoc}
     */
    public function __construct($prefixes, $fileExtension = self::DEFAULT_FILE_EXTENSION)
    {
        $locator = new SymfonyFileLocator((array) $prefixes, $fileExtension);
        parent::__construct($locator, $fileExtension);
    }
}
