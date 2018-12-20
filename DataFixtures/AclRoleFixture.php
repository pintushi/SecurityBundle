<?php

namespace Pintushi\Bundle\SecurityBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Pintushi\Bundle\SecurityBundle\Acl\Persistence\AclManager;
use Pintushi\Bundle\UserBundle\Entity\Role;
use Pintushi\Bundle\UserBundle\Repository\RoleRepository;
use Pintushi\Bundle\UserBundle\DataFixtures\RoleFixture;
use Pintushi\Bundle\SecurityBundle\Acl\Extension\ActionAclExtension;

class AclRoleFixture extends Fixture implements DependentFixtureInterface
{
    /**
     * @var AclManager
     */
    protected $aclManager;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    protected $roleRepository;

    public function __construct(AclManager $aclManager, RoleRepository $roleRepository)
    {
        $this->aclManager = $aclManager;
        $this->roleRepository = $roleRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [RoleFixture::class];
    }

    /**
     * Load ACL for security roles
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        if ($this->aclManager->isAclEnabled()) {
            $this->loadSuperAdminRole($this->aclManager);
            $this->loadManagerRole($this->aclManager);
            $this->loadUserRole($this->aclManager);
            $this->loadOrganizationRole($this->aclManager);

            $this->aclManager->flush();
        }
    }

    /**
     * @param AclManager $manager
     */
    protected function loadSuperAdminRole(AclManager $manager)
    {
        $sid = $manager->getSid($this->getRole(RoleFixture::ROLE_ADMINISTRATOR));

        foreach ($this->excludeAclExtension() as $extension) {
            $rootOid = $manager->getRootOid($extension->getExtensionKey());

            foreach ($extension->getAllMaskBuilders() as $maskBuilder) {
                $fullAccessMask = $maskBuilder->hasMask('GROUP_SYSTEM')
                    ? $maskBuilder->getMask('GROUP_SYSTEM')
                    : $maskBuilder->getMask('GROUP_ALL');

                $manager->setPermission($sid, $rootOid, $fullAccessMask, true);
            }
        }

        $this->setActionAclExtension($sid);
    }

    /**
     * @param AclManager $manager
     */
    protected function loadManagerRole(AclManager $manager)
    {
        $sid = $manager->getSid($this->getRole(RoleFixture::ROLE_MANAGER));

        foreach ($this->excludeAclExtension() as $extension) {
            $rootOid = $manager->getRootOid($extension->getExtensionKey());
            foreach ($extension->getAllMaskBuilders() as $maskBuilder) {
                if ($maskBuilder->hasMask('MASK_VIEW_SYSTEM')) {
                    $mask = $maskBuilder->getMask('MASK_VIEW_SYSTEM');
                } else {
                    $mask = $maskBuilder->getMask('GROUP_NONE');
                }
                $manager->setPermission($sid, $rootOid, $mask, true);
            }
        }
        $this->setActionAclExtension($sid);
    }

    /**
     * @param AclManager $manager
     */
    protected function loadUserRole(AclManager $manager)
    {
        $sid = $manager->getSid($this->getRole(RoleFixture::ROLE_USER));

        foreach ($this->excludeAclExtension() as $extension) {
            $rootOid = $manager->getRootOid($extension->getExtensionKey());
            foreach ($extension->getAllMaskBuilders() as $maskBuilder) {
                if ($maskBuilder->hasMask('MASK_VIEW_SYSTEM')) {
                    $mask = $maskBuilder->getMask('MASK_VIEW_SYSTEM');
                } else {
                    $mask = $maskBuilder->getMask('GROUP_NONE');
                }
                $manager->setPermission($sid, $rootOid, $mask, true);
            }
        }

        $this->setActionAclExtension($sid);
    }

    public function loadOrganizationRole(AclManager $manager)
    {
         $sid = $manager->getSid($this->getRole(RoleFixture::ROLE_ORGANIZATION));

        foreach ($this->excludeAclExtension() as $extension) {
            $rootOid = $manager->getRootOid($extension->getExtensionKey());
            foreach ($extension->getAllMaskBuilders() as $maskBuilder) {
                if ($maskBuilder->hasMask('GROUP_GLOBAL')) {
                    $mask = $maskBuilder->getMask('GROUP_GLOBAL');
                } else {
                    $mask = $maskBuilder->getMask('GROUP_NONE');
                }
                $manager->setPermission($sid, $rootOid, $mask, true);
            }
        }

        $this->setActionAclExtension($sid);
    }

    protected function setActionAclExtension($sid, $mask = 'GROUP_ALL')
    {
        $aclExtension = $this->getActionAclExtention();
        $this->setPermission($aclExtension, $sid, $mask);
    }

    protected function setPermission($extension, $sid, $mask)
    {
        $rootOid = $this->aclManager->getRootOid($extension->getExtensionKey());

        foreach ($extension->getAllMaskBuilders() as $maskBuilder) {
            if ($maskBuilder->hasMask($mask)) {
                $mask = $maskBuilder->getMask($mask);
            } else {
                $mask = $maskBuilder->getMask('GROUP_NONE');
            }

            $this->aclManager->setPermission($sid, $rootOid, $mask, true);
        }
    }

    /**
     * @param string $roleName
     * @return Role
     */
    protected function getRole($roleName)
    {
        return $this->roleRepository->findOneBy(['role' => $roleName]);
    }

    private function getActionAclExtention()
    {
        return $this->aclManager->getExtensionSelector()->selectByExtensionKey('action');
    }

    private function excludeAclExtension()
    {
        return array_filter($this->aclManager->getAllExtensions(), function ($extension) {
            return !$extension instanceof ActionAclExtension;
        });
    }
}
