<?php

namespace Pintushi\Bundle\SecurityBundle\Helper;

use Pintushi\Bundle\SecurityBundle\Acl\Domain\OneShotIsGrantedObserver;
use Pintushi\Bundle\SecurityBundle\Acl\Voter\AclVoter;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Pintushi\Bundle\SecurityBundle\Authentication\TokenAccessor;
use Pintushi\Bundle\OrganizationBundle\Entity\BusinessUnit;

class GrantedInfoHelper
{
    protected $cache = [];
    protected $aclVoter;
    protected $authorizationChecker;
    protected $tokenAccessor;

    public function __construct(
        AclVoter $aclVoter,
        AuthorizationCheckerInterface $authorizationChecker,
        TokenAccessor $tokenAccessor
    ) {
        $this->aclVoter = $aclVoter;
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenAccessor = $tokenAccessor;
    }

     /**
     * Check is granting user to object in given permission
     *
     * @param string        $permission
     * @param object|string $object
     */
    public function getGrantedInfo($permission, $object)
    {
        $user = $this->tokenAccessor->getUser();
        if (!$user) {
            return false;
        }

        $cacheKey = $this->getCacheKey($permission, $object);
        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }

        $observer = new OneShotIsGrantedObserver();
        $this->aclVoter->addOneShotIsGrantedObserver($observer);
        $isAssignGranted = $this->authorizationChecker->isGranted($permission, $object);
        $accessLevel = $observer->getAccessLevel();

        $this->cache[$cacheKey]= [
             $isAssignGranted,
             $accessLevel
        ];

        return $this->cache[$cacheKey];
    }


    private function getCacheKey($permission, $object)
    {
        if (is_object($object)) {
            return sprintf('%s:%s', spl_object_id($object), $permission);
        }

        return sprintf('%s:%s', $object, $permission);
    }
}
