<?php

namespace Pintushi\Bundle\SecurityBundle\Authentication\Token;

use Pintushi\Bundle\OrganizationBundle\Entity\Organization;

trait OrganizationContextTokenTrait
{
    /** @var  Organization */
    protected $organizationContext;

    /**
     * Returns organization
     *
     * @return Organization
     */
    public function getOrganizationContext()
    {
        return $this->organizationContext;
    }

    /**
     * Set an organization
     *
     * @param Organization $organization
     */
    public function setOrganizationContext(Organization $organization)
    {
        $this->organizationContext = $organization;
    }
}
