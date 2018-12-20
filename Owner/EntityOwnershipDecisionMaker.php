<?php

namespace Pintushi\Bundle\SecurityBundle\Owner;

use Pintushi\Bundle\SecurityBundle\Acl\Domain\ObjectIdAccessor;
use Pintushi\Bundle\SecurityBundle\Authentication\TokenAccessorInterface;
use Pintushi\Bundle\SecurityBundle\Owner\Metadata\OwnershipMetadataProviderInterface;
use Pintushi\Bundle\UserBundle\Entity\User;

class EntityOwnershipDecisionMaker extends AbstractEntityOwnershipDecisionMaker
{
    /** @var TokenAccessorInterface */
    protected $tokenAccessor;

    /**
     * @param OwnerTreeProviderInterface         $treeProvider
     * @param ObjectIdAccessor                   $objectIdAccessor
     * @param EntityOwnerAccessor                $entityOwnerAccessor
     * @param OwnershipMetadataProviderInterface $ownershipMetadataProvider
     * @param TokenAccessorInterface             $tokenAccessor
     */
    public function __construct(
        OwnerTreeProviderInterface $treeProvider,
        ObjectIdAccessor $objectIdAccessor,
        EntityOwnerAccessor $entityOwnerAccessor,
        OwnershipMetadataProviderInterface $ownershipMetadataProvider,
        TokenAccessorInterface $tokenAccessor
    ) {
        parent::__construct($treeProvider, $objectIdAccessor, $entityOwnerAccessor, $ownershipMetadataProvider);
        $this->tokenAccessor = $tokenAccessor;
    }

    /**
     * {@inheritdoc}
     */
    public function supports()
    {
        return $this->tokenAccessor->getUser() instanceof User;
    }
}
