<?php

namespace Pintushi\Bundle\SecurityBundle\EventListener;

use Videni\Bundle\RestBundle\Event\SerializationContextEvent;
use Pintushi\Bundle\SecurityBundle\Serializer\Exclusion\AclProtectedFieldExclusionStrategy;
use Pintushi\Bundle\SecurityBundle\Form\FieldAclHelper;

class SerializationContextListener
{
    private $fieldAclHelper;

    public function __construct(FieldAclHelper $fieldAclHelper)
    {
        $this->fieldAclHelper = $fieldAclHelper;
    }

    public function addAclProtectedFieldExclusionStrategy(SerializationContextEvent $event)
    {
        $context = $event->getContext();
        $context->addExclusionStrategy(new AclProtectedFieldExclusionStrategy($this->fieldAclHelper));
    }
}
