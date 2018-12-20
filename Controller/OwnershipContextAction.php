<?php

namespace Pintushi\Bundle\SecurityBundle\Controller;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\Factory\ResourceNameCollectionFactoryInterface;

use Pintushi\Bundle\SecurityBundle\ORM\DoctrineHelper;
use Pintushi\Bundle\SecurityBundle\Acl\AccessLevel;
use Pintushi\Bundle\SecurityBundle\Acl\Domain\OneShotIsGrantedObserver;
use Pintushi\Bundle\SecurityBundle\Acl\Voter\AclVoter;
use Pintushi\Bundle\SecurityBundle\Authentication\TokenAccessorInterface;
use Pintushi\Bundle\SecurityBundle\Owner\Metadata\OwnershipMetadata;
use Pintushi\Bundle\SecurityBundle\Owner\Metadata\OwnershipMetadataInterface;
use Pintushi\Bundle\SecurityBundle\Owner\Metadata\OwnershipMetadataProviderInterface;
use Pintushi\Bundle\SecurityBundle\Acl;
use Pintushi\Bundle\UserBundle\Entity\User;

class OwnershipContextAction
{
    /** @var OwnershipMetadataProviderInterface */
    protected $ownershipMetadataProvider;

    /** @var AuthorizationCheckerInterface */
    protected $authorizationChecker;

    /** @var TokenAccessorInterface */
    protected $tokenAccessor;

    private $doctrineHelper;

    private $resourceNameCollectionFactory;
    private $resourceMetadataFactory;
    protected $aclVoter;

    public function __construct(
        OwnershipMetadataProviderInterface $ownershipMetadataProvider,
        AuthorizationCheckerInterface $authorizationChecker,
        TokenAccessorInterface $tokenAccessor,
        DoctrineHelper $doctrineHelper,
        ResourceNameCollectionFactoryInterface $resourceNameCollectionFactory,
        ResourceMetadataFactoryInterface $resourceMetadataFactory,
        AclVoter $aclVoter
    ) {
        $this->ownershipMetadataProvider = $ownershipMetadataProvider;
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenAccessor = $tokenAccessor;
        $this->doctrineHelper = $doctrineHelper;
        $this->resourceNameCollectionFactory = $resourceNameCollectionFactory;
        $this->resourceMetadataFactory = $resourceMetadataFactory;
        $this->aclVoter = $aclVoter;
    }

    public function __invoke(string $shortName): array
    {
        $user = $this->getCurrentUser();
        if (!$user) {
             throw new \LogicException('You are not loggined');
        }

        $shortName= strtolower($shortName);

        $resourceClass = isset($this->getResourceClassMap()[$shortName])? $this->getResourceClassMap()[$shortName] : '';
        if (!$resourceClass) {
            throw new NotFoundHttpException();
        }

        if (!$metadata = $this->getMetadata($resourceClass)) {
            throw new NotFoundHttpException();
        }

        $observer = new OneShotIsGrantedObserver();
        $this->aclVoter->addOneShotIsGrantedObserver($observer);
        $isAssignGranted = $this->authorizationChecker->isGranted('ASSIGN', 'entity:' . $resourceClass);
        $accessLevel = $observer->getAccessLevel();

        //current user doesn't have assign permission on
        //this resource, the ownership of this class will be filled with current
        //user's automatically
        if (!$isAssignGranted) {
               throw new NotFoundHttpException();
        }

        //Organization级别，且ownership为organization
        if ($accessLevel == AccessLevel::GLOBAL_LEVEL && $metadata->isOrganizationOwned()) {
            throw new NotFoundHttpException();
        }

        if ($accessLevel == AccessLevel::SYSTEM_LEVEL) {
            $organization = [
               'name' => $metadata->getOrganizationFieldName(),
               'ownership_type' => 'organization',
            ];
        }
        if ($metadata->isBusinessUnitOwned()) {
            $owner['ownership_type'] = 'business_unit';
            $owner['name'] =  $metadata->getOwnerFieldName();
        } else if ($metadata->isUserOwned()) {
            $owner['ownership_type'] = 'user';
            $owner['name'] =  $metadata->getOwnerFieldName();
        }

        $fields =  [
            isset($organization)? $organization: null,
            isset($owner)? $owner: null,
        ];

        if (empty($fields)) {
            throw new NotFoundHttpException();
        }

        $filtered =  array_filter($fields);

        return  is_array($filtered)? $filtered: [$filtered];
    }

    private function getResourceClassMap()
    {
        $maps = [];

        foreach ($this->resourceNameCollectionFactory->create() as $resourceClass) {
            $resourceMetadata = $this->resourceMetadataFactory->create($resourceClass);
            $maps[strtolower($resourceMetadata->getShortName())] = $resourceClass;
        }

        return $maps;
    }

      /**
     * Get metadata for entity
     *
     * @param object|string $entity
     *
     * @return bool|OwnershipMetadataInterface
     */
    protected function getMetadata($entity)
    {
        if (is_object($entity)) {
            $entity = ClassUtils::getClass($entity);
        }
        if (!$this->doctrineHelper->isManageableEntity($entity)) {
            return false;
        }

        $metadata = $this->ownershipMetadataProvider->getMetadata($entity);

        return $metadata->hasOwner()
            ? $metadata
            : false;
    }

     /**
     * @return null|User
     */
    protected function getCurrentUser()
    {
        $user = $this->tokenAccessor->getUser();
        if ($user && is_object($user) && $user instanceof User) {
            return $user;
        }

        return null;
    }
}
