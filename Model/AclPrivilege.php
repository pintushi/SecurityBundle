<?php

namespace Pintushi\Bundle\SecurityBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

class AclPrivilege
{
    /**
     * @var AclPrivilegeIdentity
     */
    private $identity;

    /**
     * @var string
     */
    private $group;

    /**
     * @var string
     */
    private $extensionKey;

    /**
     * @var ArrayCollection
     */
    private $permissions;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $category;

    /**
     * @var ArrayCollection
     */
    private $fields;

    public function __construct()
    {
        $this->permissions = new ArrayCollection();
        $this->fields = new ArrayCollection();
    }

    public function getIdentity(): AclPrivilegeIdentity
    {
        return $this->identity;
    }

    public function setIdentity(AclPrivilegeIdentity $identity): AclPrivilege
    {
        $this->identity = $identity;

        return $this;
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    /**
     * @param  string       $group
     * @return AclPrivilege
     */
    public function setGroup(string $group): AclPrivilege
    {
        $this->group = $group;

        return $this;
    }

    public function getExtensionKey(): string
    {
        return $this->extensionKey;
    }

    /**
     * @param  string       $extensionKey
     * @return AclPrivilege
     */
    public function setExtensionKey(string $extensionKey): AclPrivilege
    {
        $this->extensionKey = $extensionKey;

        return $this;
    }

    /**
     * @return AclPermission[]|ArrayCollection
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    public function hasPermissions(): bool
    {
        return !$this->permissions->isEmpty();
    }

    public function addPermission(AclPermission $permission): AclPrivilege
    {
        $this->permissions->set($permission->getName(), $permission);

        return $this;
    }

    /**
     * @param AclPermission $permission
     * @return $this
     */
    public function removePermission(AclPermission $permission)
    {
        $this->permissions->removeElement($permission);

        return $this;
    }

    public function hasPermission(string $name): bool
    {
        return $this->permissions->containsKey($name);
    }

    public function getPermissionCount(): int
    {
        return $this->permissions->count();
    }

    public function setDescription(?string $description): AclPrivilege
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(?string $category): void
    {
        $this->category = $category;
    }

    public function getFields(): ArrayCollection
    {
        return $this->fields;
    }

    public function setFields(ArrayCollection $fields): void
    {
        $this->fields = $fields;
    }
}
