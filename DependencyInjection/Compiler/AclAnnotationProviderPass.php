<?php

namespace Pintushi\Bundle\SecurityBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Pintushi\Bundle\SecurityBundle\Metadata\AclAnnotationProvider;

class AclAnnotationProviderPass implements CompilerPassInterface
{
    const TAG_NAME              = 'pintushi_security.acl.config_loader';

    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(AclAnnotationProvider::class)) {
            return;
        }

        $providerDef = $container->getDefinition(AclAnnotationProvider::class);

        $loaders = $container->findTaggedServiceIds(self::TAG_NAME);
        foreach ($loaders as $id => $attributes) {
            $providerDef->addMethodCall('addLoader', array(new Reference($id)));
        }
    }
}
