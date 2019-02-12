<?php

namespace Pintushi\Bundle\SecurityBundle\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

trait OrganizationContextTokenSerializerTrait
{
    use OrganizationContextTokenTrait;

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        $organization = $this->getOrganizationContext();
        $organizationContext = is_object($organization) ? clone $organization : $organization;

        if ($this instanceof AbstractToken) {
            return serialize([$organizationContext, parent::serialize()]);
        } else {
            return serialize([$organizationContext, '']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list($organizationContext, $parentStr) = unserialize($serialized);

        $this->setOrganizationContext($organizationContext);

        if ($this instanceof AbstractToken) {
            parent::unserialize($parentStr);
        }
    }
}
