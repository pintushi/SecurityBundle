<?php

namespace Pintushi\Bundle\SecurityBundle\Annotation\Loader;

use Pintushi\Bundle\SecurityBundle\Metadata\AclAnnotationStorage;

interface AclAnnotationLoaderInterface
{
    /**
     * Loads ACL annotations
     *
     * @param AclAnnotationStorage $storage
     */
    public function load(AclAnnotationStorage $storage);
}
