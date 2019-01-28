<?php

namespace Pintushi\Bundle\SecurityBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Pintushi\Bundle\SecurityBundle\Acl\Persistence\AclManager;
use Symfony\Component\Translation\TranslatorInterface;
use Pintushi\Bundle\SecurityBundle\Event\OrganizationSwitchAfter;
use Pintushi\Bundle\SecurityBundle\Event\OrganizationSwitchBefore;

class AclPermissionController extends Controller
{
    private $aclManager;
    private $translator;

    public function __construct(AclManager $aclManager, TranslatorInterface $translator)
    {
        $this->aclManager = $aclManager;
        $this->translator = $translator;
    }

    /**
     * @Route(
     *  "/acl-access-levels/oid/{oid}/permission/{permission}",
     *  name="app_security_access_levels",
     *  requirements={"oid"=".+", "permission"="[\w]+"},
     *  defaults={
     *      "_format"="json",
     *      "_api_respond"= true
     *  }
     * )
     *
     * @param string $oid
     * @param string $permission
     *
     * @return array
     */
    public function aclAccessLevels($oid, $permission = null)
    {
        $levels = $this
            ->aclManager
            ->getAccessLevels($oid, $permission);

        $result = [];
        foreach ($levels as $accessLevel => $accessLevelLabel) {
            $result[] =   [
                'access_level' => $accessLevel,
                'access_level_label' => $this->translator->trans('app.security.access_level.'.$accessLevelLabel),
            ];
        }

        return $result;
    }
}
