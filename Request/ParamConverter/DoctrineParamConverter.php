<?php

namespace Pintushi\Bundle\SecurityBundle\Request\ParamConverter;

use Doctrine\Common\Persistence\ManagerRegistry;
use Pintushi\Bundle\SecurityBundle\Authorization\RequestAuthorizationChecker;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\DoctrineParamConverter as BaseParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class DoctrineParamConverter extends BaseParamConverter
{
    /** @var RequestAuthorizationChecker */
    protected $requestAuthorizationChecker;

    public function __construct(
        ManagerRegistry $registry = null,
        ExpressionLanguage $expressionLanguage = null,
        array $options = [],
        RequestAuthorizationChecker $requestAuthorizationChecker = null
    ) {
        parent::__construct($registry, $expressionLanguage, $options);

        $this->requestAuthorizationChecker = $requestAuthorizationChecker;
    }

    /**
     * Stores the object in the request.
     *
     * @param Request        $request
     * @param ParamConverter $configuration
     *
     * @return bool
     *
     * @throws AccessDeniedException When User doesn't have permission to the object
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $request->attributes->set('_pintushi_access_checked', false);
        $isSet = parent::apply($request, $configuration);

        if (null !== $this->requestAuthorizationChecker && $isSet) {
            $object = $request->attributes->get($configuration->getName());
            if ($object) {
                $granted = $this->requestAuthorizationChecker->isRequestObjectIsGranted($request, $object);
                if ($granted === -1) {
                    $acl = $this->requestAuthorizationChecker->getRequestAcl($request);
                    throw new AccessDeniedException(
                        'You do not get ' . $acl->getPermission() . ' permission for this object'
                    );
                } elseif ($granted === 1) {
                    $request->attributes->set('_pintushi_access_checked', true);
                }
            }
        }

        return $isSet;
    }
}
