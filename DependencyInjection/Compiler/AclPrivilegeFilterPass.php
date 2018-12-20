<?php

namespace Pintushi\Bundle\SecurityBundle\DependencyInjection\Compiler;

use Pintushi\Component\DependencyInjection\Compiler\TaggedServicesCompilerPassTrait;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AclPrivilegeFilterPass implements CompilerPassInterface
{
    use TaggedServicesCompilerPassTrait;

    const EXTENSION_TAG = 'pintushi.security.filter.acl_privilege';
    const SERVICE_ID = 'pintushi_security.filter.configurable_permission_filter';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->registerTaggedServices($container, self::SERVICE_ID, self::EXTENSION_TAG, 'addConfigurableFilter');
    }
}
