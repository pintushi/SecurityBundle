<?php

namespace Pintushi\Bundle\SecurityBundle\EventListener;

use Videni\Bundle\RestBundle\Event\SerializationContextEvent;
use Pintushi\Bundle\SecurityBundle\Serializer\Exclusion\AclProtectedFieldExclusionStrategy;

class SerializationContextListener
{
    public function addAclProtectedFieldExclusionStrategy(SerializationContextEvent $event)
    {
        $context = $event->getContext();
        $context->addExclusionStrategy(new AclProtectedFieldExclusionStrategy());
    }
}
