<?php

namespace Pintushi\Bundle\SecurityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Translation\TranslatorInterface;
use Pintushi\Bundle\SecurityBundle\Event\OrganizationSwitchAfter;
use Pintushi\Bundle\SecurityBundle\Event\OrganizationSwitchBefore;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Pintushi\Bundle\SecurityBundle\Authentication\Token\OrganizationContextTokenInterface;
use Pintushi\Bundle\OrganizationBundle\Entity\Organization;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationSuccessResponse;
use Symfony\Component\Security\Core\User\UserInterface;

class SwitchOrganizationAction  extends Controller
{
    private $tokenStorage;
    private $eventDispatcher;
    private $translator;
    protected $jwtManager;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        EventDispatcherInterface $eventDispatcher,
        TranslatorInterface $translator,
        JWTTokenManagerInterface $jwtManager
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->eventDispatcher = $eventDispatcher;
        $this->translator = $translator;
        $this->jwtManager = $jwtManager;
    }

    public function __invoke(Organization $data)
    {
        $organization = $data;

        $token = $this->tokenStorage->getToken();
        $user  = $token->getUser();

        if (!$token instanceof OrganizationContextTokenInterface ||
            !$token->getUser() instanceof UserInterface ||
            !$organization->isEnabled() ||
            !$token->getUser()->getOrganizations()->contains($organization)
        ) {
            breakhere();
            throw new AccessDeniedException(
                $this->translator->trans(
                    'pintushi.security.organization.access_denied',
                    ['%organization_name%' => $organization->getName()]
                )
            );
        }

        $event = new OrganizationSwitchBefore($user, $token->getOrganizationContext(), $organization);
        $this->eventDispatcher->dispatch(OrganizationSwitchBefore::NAME, $event);
        $organization = $event->getOrganizationToSwitch();

        if (!$user->getOrganizations(true)->contains($organization)) {
            $message = $this->translator
                ->trans('pintushi.security.organization.access_denied', ['%organization_name%' => $organization->getName()]);

            throw new AccessDeniedException($message);
        }

        $event = new OrganizationSwitchAfter($user, $organization);
        $this->eventDispatcher->dispatch(OrganizationSwitchAfter::NAME, $event);

        $jwt = $this->jwtManager->create($user, ['organization' => $organization->getId()]);

        $response = new JWTAuthenticationSuccessResponse($jwt);

        return $response;
    }
}
