<?php

namespace Pintushi\Bundle\SecurityBundle\EventListener;

use Pintushi\Bundle\EntityConfigBundle\Event\PreFlushConfigEvent;
use Pintushi\Bundle\SecurityBundle\Owner\Metadata\OwnershipMetadataProviderInterface;

class OwnershipConfigListener
{
    /** @var OwnershipMetadataProviderInterface */
    protected $provider;

    /**
     * @param OwnershipMetadataProviderInterface $provider
     */
    public function __construct(OwnershipMetadataProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @param PreFlushConfigEvent $event
     */
    public function preFlush(PreFlushConfigEvent $event)
    {
        $config = $event->getConfig('extend');
        if (null === $config || $event->isFieldConfig()) {
            return;
        }

        $className = $config->getId()->getClassName();
        $this->provider->clearCache($className);

        $changeSet = $event->getConfigManager()->getConfigChangeSet($config);
        $isDeleted = isset($changeSet['state']);
        if (!$isDeleted) {
            $this->provider->warmUpCache($className);
        }
    }
}
