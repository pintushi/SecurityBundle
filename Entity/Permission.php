<?php

namespace Pintushi\Bundle\SecurityBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Permission
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $label;

    /**
     * If true permission will be applied for all entities in application except entities,
     * that specified in $this->excludeEntities. In this case you do not need to specify any entity in property
     * $this->applyToEntities.
     * If false permission will be applied for entities that specified in $this->applyToEntities.
     *
     * @var boolean
     */
    protected $applyToAll = true;

    /**
     * Array of entity class names. You need to specify entity classes for which you want apply current permission.
     * This property is used only in the case when $this->applyToAll is false.
     *
     * @var Collection|PermissionEntity[]
     **/
    protected $applyToEntities;

    /**
     * Array of entity class names. You need to specify entity classes for which you want not apply current permission.
     * This property is used only in the case when $this->applyToAll is true.
     *
     * @var Collection|PermissionEntity[]
     **/
    protected $excludeEntities;

    /**
     * @var array
     */
    protected $groupNames;

    /**
     * @var string
     */
    protected $description;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->applyToEntities = new ArrayCollection();
        $this->excludeEntities = new ArrayCollection();
        $this->groupNames = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel(string $label)
    {
        $this->label = $label;

        return $this;
    }

    public function isApplyToAll(): bool
    {
        return $this->applyToAll;
    }

    /**
     * @param boolean $applyToAll
     * @return $this
     */
    public function setApplyToAll(bool $applyToAll)
    {
        $this->applyToAll = $applyToAll;

        return $this;
    }

    /**
     * @return Collection|PermissionEntity[]
     */
    public function getApplyToEntities()
    {
        return $this->applyToEntities;
    }

    /**
     * @param Collection|PermissionEntity[] $applyToEntities
     * @return $this
     */
    public function setApplyToEntities(Collection $applyToEntities = null)
    {
        $this->applyToEntities = $applyToEntities;

        return $this;
    }

    /**
     * @param PermissionEntity $permissionEntity
     * @return $this
     */
    public function addApplyToEntity(PermissionEntity $permissionEntity)
    {
        if (!$this->applyToEntities->contains($permissionEntity)) {
            $this->applyToEntities->add($permissionEntity);
        }

        return $this;
    }

    /**
     * @param PermissionEntity $permissionEntity
     * @return $this
     */
    public function removeApplyToEntity(PermissionEntity $permissionEntity)
    {
        if ($this->applyToEntities->contains($permissionEntity)) {
            $this->applyToEntities->removeElement($permissionEntity);
        }

        return $this;
    }

    /**
     * @return Collection|PermissionEntity[]
     */
    public function getExcludeEntities()
    {
        return $this->excludeEntities;
    }

    /**
     * @param Collection|PermissionEntity[] $excludeEntities
     * @return $this
     */
    public function setExcludeEntities(Collection $excludeEntities = null)
    {
        $this->excludeEntities = $excludeEntities;

        return $this;
    }

    /**
     * @param PermissionEntity $permissionEntity
     * @return $this
     */
    public function addExcludeEntity(PermissionEntity $permissionEntity)
    {
        if (!$this->excludeEntities->contains($permissionEntity)) {
            $this->excludeEntities->add($permissionEntity);
        }

        return $this;
    }

    /**
     * @param PermissionEntity $permissionEntity
     * @return $this
     */
    public function removeExcludeEntity(PermissionEntity $permissionEntity)
    {
        if ($this->excludeEntities->contains($permissionEntity)) {
            $this->excludeEntities->removeElement($permissionEntity);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getGroupNames(): array
    {
        return $this->groupNames;
    }

    /**
     * @param array $groupNames
     * @return $this
     */
    public function setGroupNames(array $groupNames = null)
    {
        $this->groupNames = $groupNames;

        return $this;
    }

    /**
     * @param string $groupName
     * @return $this
     */
    public function addGroupName(string $groupName)
    {
        if (!$this->groupNames->contains($groupName)) {
            $this->groupNames->add($groupName);
        }

        return $this;
    }

    /**
     * @param string $groupName
     * @return $this
     */
    public function removeGroupName(string $groupName)
    {
        if ($this->groupNames->contains($groupName)) {
            $this->groupNames->removeElement($groupName);
        }

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description)
    {
        $this->description = $description;

        return $this;
    }

    public function import(Permission $permission): Permission
    {
        $this->setName($permission->getName())
            ->setLabel($permission->getLabel())
            ->setApplyToAll($permission->isApplyToAll())
            ->setApplyToEntities($permission->getApplyToEntities())
            ->setExcludeEntities($permission->getExcludeEntities())
            ->setGroupNames($permission->getGroupNames())
            ->setDescription($permission->getDescription());

        return $this;
    }
}
