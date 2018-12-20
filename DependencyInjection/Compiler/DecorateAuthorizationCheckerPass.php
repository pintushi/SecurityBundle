<?php

namespace Pintushi\Bundle\SecurityBundle\DependencyInjection\Compiler;

use Pintushi\Bundle\SecurityBundle\Authorization\AuthorizationChecker;
use Pintushi\Component\DependencyInjection\ServiceLink;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DecorateAuthorizationCheckerPass implements CompilerPassInterface
{
    const AUTHORIZATION_CHECKER = 'pintushi_security.authorization_checker';
    const DECORATED_AUTHORIZATION_CHECKER = 'pintushi_security.authorization_checker.inner';
    const DEFAULT_AUTHORIZATION_CHECKER = 'security.authorization_checker';
    const ACL_OBJECT_IDENTITY_FACTORY = 'pintushi_security.acl.object_identity_factory';
    const ACL_ANNOTATION_PROVIDER = 'pintushi_security.acl.annotation_provider';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $this->configureAuthorizationChecker($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function configureAuthorizationChecker(ContainerBuilder $container)
    {
        $container->register('pintushi_security.authorization_checker', AuthorizationChecker::class)
            ->setPublic(false)
            ->setDecoratedService(self::DEFAULT_AUTHORIZATION_CHECKER, 'pintushi_security.authorization_checker.inner')
            ->setArguments([
                new reference(self::DECORATED_AUTHORIZATION_CHECKER),
                new Reference(self::ACL_OBJECT_IDENTITY_FACTORY),
                new Reference(self::ACL_ANNOTATION_PROVIDER),
                new Reference('logger')
            ])
            ->addTag('monolog.logger', ['channel' => 'security']);
    }
}
