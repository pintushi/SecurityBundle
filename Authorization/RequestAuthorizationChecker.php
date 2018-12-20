<?php

namespace Pintushi\Bundle\SecurityBundle\Authorization;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

use Pintushi\Bundle\SecurityBundle\ORM\EntityClassResolver;
use Pintushi\Bundle\SecurityBundle\Annotation\Acl as AclAnnotation;
use Pintushi\Bundle\SecurityBundle\Metadata\AclAnnotationProvider;

class RequestAuthorizationChecker
{
    /** @var AuthorizationCheckerInterface */
    private $authorizationChecker;

    /** @var ServiceLink */
    private $entityClassResolver;

    /** @var ServiceLink */
    private $annotationProvider;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param EntityClassResolver                   $entityClassResolver
     * @param AclAnnotationProvider                   $annotationProvider
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        EntityClassResolver $entityClassResolver,
        AclAnnotationProvider $annotationProvider
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->entityClassResolver = $entityClassResolver;
        $this->annotationProvider = $annotationProvider;
    }

    /**
     * Check access for object for current controller action which was taken from request object.
     *
     * @param Request $request
     * @param mixed   $object
     *
     * @return int -1 if no access, 0 if can't decide, 1 if access is granted
     */
    public function isRequestObjectIsGranted(Request $request, $object)
    {
        $aclAnnotation = $this->getRequestAcl($request, true);
        if ($aclAnnotation) {
            $class = $aclAnnotation->getClass();
            $permission = $aclAnnotation->getPermission();
            if ($permission && $class && is_a($object, $class)) {
                return $this->authorizationChecker->isGranted($permission, $object) ? 1 : -1;
            }
        }

        return 0;
    }

    /**
     * Get ACL annotation object for current controller action which was taken from request object.
     *
     * @param Request $request
     * @param bool    $convertClassName
     *
     * @return AclAnnotation|null
     */
    public function getRequestAcl(Request $request, $convertClassName = false)
    {
        $controller = $request->attributes->get('_controller');
        if (false === strpos($controller, '::')) {
            return null;
        }

        $controllerData = explode('::', $controller);
        $annotation = $this->getAnnotation($controllerData[0], $controllerData[1]);
        if ($convertClassName && null !== $annotation) {
            $entityClass = $annotation->getClass();
            if ($entityClass) {
                /** @var EntityClassResolver $entityClassResolver */
                $entityClassResolver = $this->entityClassResolver->getService();
                if ($entityClassResolver->isEntity($entityClass)) {
                    $annotation->setClass($entityClassResolver->getEntityClass($entityClass));
                }
            }
        }

        return $annotation;
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
        $annotationProvider = $this->annotationProvider->getService();

        return $annotationProvider->findAnnotation($class, $method);
    }
}
