<?php

namespace Pintushi\Bundle\SecurityBundle\Acl\Domain;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Pintushi\Bundle\SecurityBundle\Acl\Extension\AclExtensionInterface;

/**
 * Provides an interface which can be implemented by AclVoter to allow
 * the underlying permission granting strategy to get
 * an object which is the subject of the current voting operation
 * and the security token of the current voting operation.
 */
interface PermissionGrantingStrategyContextInterface
{
    /**
     * Gets the current object from a context
     *
     * @return mixed
     */
    public function getObject();

    /**
     * Gets the security token from a context
     *
     * @return TokenInterface
     */
    public function getSecurityToken();

    /**
     * Gets the ACL extension responsible to process the current object
     *
     * @return AclExtensionInterface
     */
    public function getAclExtension();

    /**
     * Sets a mask was used to decide whether the access to a resource is granted or denied.
     *
     * @param int $mask
     * @param int $accessLevel
     */
    public function setTriggeredMask($mask, $accessLevel);
}
