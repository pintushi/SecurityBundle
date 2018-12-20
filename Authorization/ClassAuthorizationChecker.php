<?php

namespace Pintushi\Bundle\SecurityBundle\Authorization;

use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

use Pintushi\Bundle\SecurityBundle\Acl\Domain\ObjectIdentityFactory;
use Pintushi\Bundle\SecurityBundle\Annotation\Acl as AclAnnotation;
use Pintushi\Bundle\SecurityBundle\Metadata\AclAnnotationProvider;

class ClassAuthorizationChecker
{
    /** @var AuthorizationCheckerInterface */
    private $authorizationChecker;

    /** @var ServiceLink */
    private $objectIdentityFactory;

    /** @var ServiceLink */
    private $annotationProviderLink;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param ServiceLink                   $objectIdentityFactory
     * @param AclAnnotationProvider                   $annotationProviderLink
     * @param LoggerInterface               $logger
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        ObjectIdentityFactory $objectIdentityFactory,
        AclAnnotationProvider $annotationProviderLink,
        LoggerInterface $logger
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->objectIdentityFactory = $objectIdentityFactory;
        $this->annotationProviderLink = $annotationProviderLink;
        $this->logger = $logger;
    }

    /**
     * Checks if an access to the given method of the given class is granted for the current authentication token.
     *
     * @param  string $class
     * @param  string $method
     *
     * @return bool
     */
    public function isClassMethodGranted($class, $method)
    {
        $isGranted = true;

        // check method level ACL
        $annotation = $this->getAnnotation($class, $method);
        if (null !== $annotation) {
            $this->logger->debug(
                sprintf('Check an access using "%s" ACL annotation.', $annotation->getId())
            );
            $isGranted = $this->authorizationChecker->isGranted(
                $annotation->getPermission(),
                $this->getObjectIdentity($annotation)
            );
        }

        // check class level ACL
        if ($isGranted && (null === $annotation || !$annotation->getIgnoreClassAcl())) {
            $annotation = $this->getAnnotation($class);
            if (null !== $annotation) {
                $this->logger->debug(
                    sprintf('Check an access using "%s" ACL annotation.', $annotation->getId())
                );
                $isGranted = $this->authorizationChecker->isGranted(
                    $annotation->getPermission(),
                    $this->getObjectIdentity($annotation)
                );
            }
        }

        return $isGranted;
    }

    /**
     * Gets ACL annotation is bound to the given class/method.
     *
     * @param string $class
     * @param string $method
     *
     * @return AclAnnotation|null
     */
    public function getClassMethodAnnotation($class, $method)
    {
        return $this->getAnnotation($class, $method);
    }

    /**
     * @param string      $class
     * @param string|null $method
     *
     * @return AclAnnotation|null
     */
    private function getAnnotation($class, $method = null)
    {
        /** @var AclAnnotationProvider $annotationProvider */
        $annotationProvider = $this->annotationProviderLink->getService();

        return $annotationProvider->findAnnotation($class, $method);
    }

    /**
     * @param mixed $val
     *
     * @return ObjectIdentity
     */
    private function getObjectIdentity($val)
    {
        /** @var ObjectIdentityFactory $objectIdentityFactory */
        $objectIdentityFactory = $this->objectIdentityFactory->getService();

        return $objectIdentityFactory->get($val);
    }
}
