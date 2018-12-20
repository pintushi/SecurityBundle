<?php

namespace Pintushi\Bundle\SecurityBundle\Annotation;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

use Pintushi\Bundle\SecurityBundle\Annotation\Loader\AclAnnotationLoader;
use Pintushi\Bundle\SecurityBundle\DependencyInjection\PintushiSecurityExtension;
use Pintushi\Bundle\SecurityBundle\Metadata\AclAnnotationProvider;
use Pintushi\Bundle\SecurityBundle\Metadata\ActionMetadataProvider;
use Pintushi\Component\Config\Dumper\ConfigMetadataDumperInterface;
use Pintushi\Component\Config\Dumper\CumulativeConfigMetadataDumper;

class AclListener
{
    /** @var AclAnnotationProvider */
    protected $cacheProvider;

    /** @var CumulativeConfigMetadataDumper */
    protected $dumper;

    /**
     * @param AclAnnotationProvider $cacheProvider
     * @param ActionMetadataProvider $actionProvider
     * @param ConfigMetadataDumperInterface $dumper
     */
    public function __construct(
        AclAnnotationProvider $cacheProvider,
        ActionMetadataProvider $actionProvider,
        ConfigMetadataDumperInterface $dumper
    ) {
        $this->cacheProvider = $cacheProvider;
        $this->actionMetadataProvider = $actionProvider;
        $this->dumper = $dumper;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        if (!$this->dumper->isFresh()) {
            $this->cacheProvider->warmUpCache();
            $this->actionMetadataProvider->warmUpCache();

            $tempAclContainer = new ContainerBuilder();
            $loader = AclAnnotationLoader::getAclAnnotationResourceLoader();
            $loader->registerResources($tempAclContainer);
            $loader = PintushiSecurityExtension::getAclConfigLoader();
            $loader->registerResources($tempAclContainer);

            $this->dumper->dump($tempAclContainer);
        }
    }
}
