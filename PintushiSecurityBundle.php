<?php

namespace Pintushi\Bundle\SecurityBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Oro\Component\DependencyInjection\Compiler\ServiceLinkCompilerPass;
use Pintushi\Bundle\SecurityBundle\DependencyInjection\Compiler;

final class PintushiSecurityBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ServiceLinkCompilerPass());
        $container->addCompilerPass(new Compiler\AclConfigurationPass());
        $container->addCompilerPass(new Compiler\AclAnnotationProviderPass());
        $container->addCompilerPass(new Compiler\OwnershipDecisionMakerPass());
        $container->addCompilerPass(new Compiler\AclPrivilegeFilterPass());
        $container->addCompilerPass(new Compiler\AccessRulesPass());
    }
}
