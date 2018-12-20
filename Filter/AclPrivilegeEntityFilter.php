<?php

namespace Pintushi\Bundle\SecurityBundle\Filter;

use Pintushi\Bundle\SecurityBundle\Acl\Extension\ObjectIdentityHelper;
use Pintushi\Bundle\SecurityBundle\Model\AclPrivilege;
use Pintushi\Bundle\SecurityBundle\Model\ConfigurablePermission;

class AclPrivilegeEntityFilter implements AclPrivilegeConfigurableFilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function filter(AclPrivilege $aclPrivilege, ConfigurablePermission $configurablePermission)
    {
        $entityClass = ObjectIdentityHelper::getClassFromIdentityString($aclPrivilege->getIdentity()->getId());

        foreach ($aclPrivilege->getPermissions() as $permissionName => $permission) {
            if (!$configurablePermission->isEntityPermissionConfigurable($entityClass, $permissionName)) {
                $aclPrivilege->removePermission($permission);
            }
        }

        return $aclPrivilege->hasPermissions();
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported(AclPrivilege $aclPrivileges)
    {
        $identity = $aclPrivileges->getIdentity();
        return ObjectIdentityHelper::getExtensionKeyFromIdentityString($identity->getId()) === 'entity';
    }
}
