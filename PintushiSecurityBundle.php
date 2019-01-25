<?php

namespace Pintushi\Bundle\SecurityBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Pintushi\Component\DependencyInjection\Compiler\ServiceLinkCompilerPass;
use Pintushi\Bundle\SecurityBundle\DependencyInjection\Compiler as CompilerPasses;


final class PintushiSecurityBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ServiceLinkCompilerPass());
        $container->addCompilerPass(new CompilerPasses\AclConfigurationPass());
        $container->addCompilerPass(new CompilerPasses\QueryHintResolverPass());
        $container->addCompilerPass(new CompilerPasses\AclAnnotationProviderPass());
        $container->addCompilerPass(new CompilerPasses\OwnershipDecisionMakerPass());
        $container->addCompilerPass(new CompilerPasses\AclPrivilegeFilterPass());
        $container->addCompilerPass(new CompilerPasses\AccessRulesPass());
    }
}
