<?php

namespace Pintushi\Bundle\SecurityBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Pintushi\Bundle\OrganizationBundle\Entity\Organization;
use Pintushi\Bundle\SecurityBundle\Acl\Domain\ObjectIdentityFactory;
use Pintushi\Bundle\SecurityBundle\Acl\Extension\ObjectIdentityHelper;
use Pintushi\Bundle\SecurityBundle\Authentication\Token\OrganizationContextTokenInterface;
use Pintushi\Bundle\SecurityBundle\Event\OrganizationSwitchAfter;
use Pintushi\Bundle\SecurityBundle\Event\OrganizationSwitchBefore;
use Pintushi\Bundle\UserBundle\Entity\User;

class AclPermissionController extends Controller
{
    /**
     * @Route(
     *  "/acl-access-levels/{oid}/{permission}",
     *  name="pintushi_security_access_levels",
     *  requirements={"oid"="[\w]+:[\w\:\(\)\|]+", "permission"="[\w/]+"},
     *  defaults={"_format"="json", "permission"=null}
     * )
     * @Template
     *
     * @param string $oid
     * @param string $permission
     *
     * @return array
     */
    public function aclAccessLevelsAction($oid, $permission = null)
    {
        if (ObjectIdentityHelper::getExtensionKeyFromIdentityString($oid) === 'entity') {
            $entity = ObjectIdentityHelper::getClassFromIdentityString($oid);
            if ($entity !== ObjectIdentityFactory::ROOT_IDENTITY_TYPE) {
                if (ObjectIdentityHelper::isFieldEncodedKey($entity)) {
                    list($className, $fieldName) = ObjectIdentityHelper::decodeEntityFieldInfo($entity);
                    $oid = ObjectIdentityHelper::encodeIdentityString(
                        'entity',
                        ObjectIdentityHelper::encodeEntityFieldInfo(
                            $this->get('oro_entity.routing_helper')->resolveEntityClass($className),
                            $fieldName
                        )
                    );
                } else {
                    $oid = ObjectIdentityHelper::encodeIdentityString(
                        'entity',
                        $this->get('oro_entity.routing_helper')->resolveEntityClass($entity)
                    );
                }
            }
        }

        $levels = $this
            ->get('oro_security.acl.manager')
            ->getAccessLevels($oid, $permission);

        return ['levels' => $levels];
    }
}
