<?php

namespace Pintushi\Bundle\SecurityBundle\Authentication\Token;

use Symfony\Component\Security\Core\User\UserInterface;
use Pintushi\Bundle\OrganizationBundle\Entity\OrganizationInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken;

class OrganizationToken extends JWTUserToken implements OrganizationContextTokenInterface
{
    use OrganizationContextTokenTrait;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $roles = [], UserInterface $user = null, $rawToken = null, $providerKey = null, OrganizationInterface $organization = null)
    {
        parent::__construct($roles, $user, $rawToken, $providerKey);

        $this->setOrganizationContext($organization);
    }
}
