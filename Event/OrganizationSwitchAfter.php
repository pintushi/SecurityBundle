<?php

namespace Pintushi\Bundle\SecurityBundle\Event;

use Pintushi\Bundle\OrganizationBundle\Entity\Organization;
use Pintushi\Bundle\UserBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class OrganizationSwitchAfter extends Event
{
    const NAME = 'pintushi_security.event.organization_switch.after';

    /** @var User */
    protected $user;

    /** @var Organization */
    protected $organization;

    /**
     * @param User         $user
     * @param Organization $organization
     */
    public function __construct(User $user, Organization $organization)
    {
        $this->user         = $user;
        $this->organization = $organization;
    }

    /**
     * Current active organization
     *
     * @return Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Current logged in user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}
