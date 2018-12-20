<?php

namespace Pintushi\Bundle\SecurityBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Pintushi\Component\DependencyInjection\Compiler\ServiceLinkCompilerPass;
use Pintushi\Bundle\SecurityBundle\DependencyInjection\Compiler\AclConfigurationPass;
use Pintushi\Bundle\SecurityBundle\DependencyInjection\Compiler\QueryHintResolverPass;
use Pintushi\Bundle\SecurityBundle\DependencyInjection\Compiler\AclAnnotationProviderPass;
use Pintushi\Bundle\SecurityBundle\DependencyInjection\Compiler\OwnershipDecisionMakerPass;
use Pintushi\Bundle\SecurityBundle\DependencyInjection\Compiler\AclPrivilegeFilterPass;

final class PintushiSecurityBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ServiceLinkCompilerPass());
        $container->addCompilerPass(new AclConfigurationPass());
        $container->addCompilerPass(new QueryHintResolverPass());
        $container->addCompilerPass(new AclAnnotationProviderPass());
        $container->addCompilerPass(new OwnershipDecisionMakerPass());
        $container->addCompilerPass(new AclPrivilegeFilterPass());
    }
}
