<?php

namespace Pintushi\Bundle\SecurityBundle\Filter;

use Pintushi\Bundle\SecurityBundle\Acl\Extension\ObjectIdentityHelper;
use Pintushi\Bundle\SecurityBundle\Model\AclPrivilege;
use Pintushi\Bundle\SecurityBundle\Model\ConfigurablePermission;

class AclPrivilegeCapabilityFilter implements AclPrivilegeConfigurableFilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function filter(AclPrivilege $aclPrivilege, ConfigurablePermission $configurablePermission)
    {
        $identity = $aclPrivilege->getIdentity();
        $capability = ObjectIdentityHelper::getClassFromIdentityString($identity->getId());

        return $configurablePermission->isCapabilityConfigurable($capability);
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported(AclPrivilege $aclPrivileges)
    {
        $identity = $aclPrivileges->getIdentity();

        return ObjectIdentityHelper::getExtensionKeyFromIdentityString($identity->getId()) === 'action';
    }
}
