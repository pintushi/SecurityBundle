<?php

namespace Pintushi\Bundle\SecurityBundle\ORM\Walker;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Pintushi\Component\DoctrineUtils\ORM\QueryWalkerHintProviderInterface;
use Pintushi\Bundle\SecurityBundle\Authentication\Token\OrganizationContextTokenInterface;
use Pintushi\Bundle\UserBundle\Entity\AbstractUser;
use Pintushi\Bundle\SecurityBundle\Authentication\TokenAccessor;

class CurrentUserWalkerHintProvider implements QueryWalkerHintProviderInterface
{
    /** @var TokenStorageInterface */
    protected $tokenAccessor;

    /**
     * @param TokenAccessor $tokenAccessor
     */
    public function __construct(TokenAccessor $tokenAccessor)
    {
        $this->tokenAccessor = $tokenAccessor;
    }

    /**
     * {@inheritdoc}
     */
    public function getHints($params)
    {
        $securityContext = [];

        $token = $this->tokenAccessor->getToken();
        if ($token) {
            $user = $token->getUser();
            if ($user instanceof AbstractUser) {
                $field                   = is_array($params) && isset($params['user_field'])
                    ? $params['user_field']
                    : 'owner';
                $securityContext[$field] = $user->getId();
                if ($token instanceof OrganizationContextTokenInterface) {
                    $field                   = is_array($params) && isset($params['organization_field'])
                        ? $params['organization_field']
                        : 'organization';
                    $securityContext[$field] = $token->getOrganizationContext()->getId();
                }
            }
        }

        return [
            CurrentUserWalker::HINT_SECURITY_CONTEXT => $securityContext
        ];
    }
}
