<?php

namespace Pintushi\Bundle\SecurityBundle\Model;

use Doctrine\ORM\Mapping as ORM;

class ConfigurablePermission
{
    /** @var string */
    private $name;

    /** @var array[]|bool */
    private $entities;

    /** @var array */
    private $capabilities = [];


    /** @var bool */
    private $default;

    /**
     * @param string $name
     * @param bool $default
     * @param array[]|bool $entities
     * @param array $capabilities
     * @param array[]|bool $workflows
     */
    public function __construct(
        $name,
        $default = true,
        $entities = [],
        array $capabilities = []
    ) {
        $this->name = $name;
        $this->default = (bool)$default;
        $this->entities = $entities;
        $this->capabilities = $capabilities;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $entityClass
     * @param string $permission
     * @return bool
     */
    public function isEntityPermissionConfigurable($entityClass, $permission)
    {
        if (!isset($this->entities[$entityClass])) {
            return (bool) $this->default;
        }
        $permissions = $this->entities[$entityClass];
        // if boolean value - it using for all permissions
        if (is_bool($permissions)) {
            return $permissions;
        }

        return (bool) isset($permissions[$permission]) ? $permissions[$permission] : $this->default;
    }

    /**
     * @param string $capability
     * @return bool
     */
    public function isCapabilityConfigurable($capability)
    {
        $capabilities = $this->capabilities ?: [];

        return (bool) isset($capabilities[$capability]) ? $capabilities[$capability] : $this->default;
    }
}
