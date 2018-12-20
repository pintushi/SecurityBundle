<?php

namespace Pintushi\Bundle\SecurityBundle\Entity;

class PermissionEntity
{
    /**
     * @var int
     *
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

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
}
