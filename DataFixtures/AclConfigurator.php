<?php

namespace Pintushi\Bundle\SecurityBundle\DataFixtures;

use Pintushi\Bundle\SecurityBundle\Acl\Persistence\AclManager;
use Pintushi\Bundle\UserBundle\Entity\Role;
use Pintushi\Bundle\UserBundle\Entity\User;
use Pintushi\Bundle\UserBundle\Repository\RoleRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Acl\Model\SecurityIdentityInterface;

class AclConfigurator
{
    const ALL_ROLES = '*';

    private $aclManager;

    private $roleRepository;

    private $objectManager;

    public function __construct(AclManager $aclManager, RoleRepository $roleRepository, ObjectManager $objectManager)
    {
        $this->aclManager = $aclManager;
        $this->roleRepository = $roleRepository;
        $this->objectManager = $objectManager;
    }

    public function process(array $aclData)
    {
          /** @var AclManager $aclManager */
        if (!$this->aclManager->isAclEnabled()) {
            return;
        }

        foreach ($aclData as $roleName => $roleConfigData) {
            if (self::ALL_ROLES === $roleName) {
                foreach ($this->getRoles() as $role) {
                    $this->processRole($role, $roleConfigData);
                }
            } else {
                $role = $this->getRole($roleName, $roleConfigData);
                if (!$role) {
                    continue;
                }

                $this->processRole($role, $roleConfigData);
            }
        }

        $this->aclManager->flush();
    }

    /**
     * Sets ACL
     *
     * @param mixed      $sid
     * @param string     $permission
     * @param array      $acls
     */
    protected function processPermission(
        SecurityIdentityInterface $sid,
        $permission,
        array $acls
    ) {
        $oid = $this->aclManager->getOid(str_replace('|', ':', $permission));

        $extension = $this->aclManager->getExtensionSelector()->select($oid);
        $maskBuilders = $extension->getAllMaskBuilders();

        foreach ($maskBuilders as $maskBuilder) {
            $mask = $maskBuilder->reset()->get();

            if (!empty($acls)) {
                foreach ($acls as $acl) {
                    if ($maskBuilder->hasMask($acl)) {
                        $mask = $maskBuilder->add($maskBuilder->getMask($acl))->get();
                    }
                }
            }

            $this->aclManager->setPermission($sid, $oid, $mask);
        }
    }



    /**
     * Gets Role instance
     *
     * @param string        $roleName
     * @param array         $roleConfigData
     *
     * @return Role|null
     */
    protected function getRole($roleName, $roleConfigData)
    {
        if (!empty($roleConfigData['role'])) {
            $roleName = $roleConfigData['role'];
        }

        return $this->roleRepository->findOneBy(['role' => $roleName]);
    }

    /**
     * Sets Role permissions
     *
     * @param Role          $role
     * @param array         $roleConfigData
     */
    protected function processRole(Role $role, array $roleConfigData)
    {
        if (isset($roleConfigData['label'])) {
            $role->setLabel($roleConfigData['label']);
        }

        if (!$role->getId()) {
            $this->objectManager->persist($role);
        }

        if (isset($roleConfigData['permissions'])) {
            $sid = $this->aclManager->getSid($role);
            foreach ($roleConfigData['permissions'] as $permission => $acls) {
                $this->processPermission($sid, $permission, $acls);
            }
        }
    }

    /**
     * Returns all roles, some filter can be applied here
     *
     * @return Role[]
     */
    protected function getRoles()
    {
        return $this->roleRepository
            ->createQueryBuilder('r')
            ->where('r.role <> :role')
            ->setParameter('role', User::ROLE_ANONYMOUS)
            ->getQuery()
            ->getResult();
    }
}
