<?php

namespace Pintushi\Bundle\SecurityBundle\Authentication;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Pintushi\Bundle\OrganizationBundle\Entity\Organization;
use Pintushi\Bundle\UserBundle\Entity\User;

interface TokenAccessorInterface extends TokenStorageInterface
{
    /**
     * Checks whether an user entity exists in the current security token.
     *
     * @return bool
     */
    public function hasUser();

    /**
     * Gets an user entity from the current security token.
     *
     * @return User|null
     */
    public function getUser();

    /**
     * Gets identifier of an user entity from the current security token.
     *
     * @return int|null
     */
    public function getUserId();

    /**
     * Gets an organization entity from the current security token.
     *
     * @return Organization|null
     */
    public function getOrganization();

    /**
     * Gets identifier of an organization entity from the current security token.
     *
     * @return int|null
     */
    public function getOrganizationId();
}
