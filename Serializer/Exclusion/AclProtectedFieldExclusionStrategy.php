<?php

namespace Pintushi\Bundle\SecurityBundle\Serializer\Exclusion;

use JMS\Serializer\Context;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Exclusion\ExclusionStrategyInterface;
use Pintushi\Bundle\SecurityBundle\Form\FieldAclHelper;

class AclProtectedFieldExclusionStrategy implements ExclusionStrategyInterface
{
    private $fieldAclHelper;

    private $cache = [];

    public function __construct(FieldAclHelper $fieldAclHelper)
    {
        $this->fieldAclHelper = $fieldAclHelper;
    }

    /**
     * {@inheritDoc}
     */
    public function shouldSkipClass(ClassMetadata $metadata, Context $navigatorContext)
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function shouldSkipProperty(PropertyMetadata $property, Context $navigatorContext)
    {
        $class = $property->class;
        $name = $property->name;

        if (isset($this->cache[$class]) && isset($this->cache[$class][$name])) {
            return $this->cache[$class][$name];
        }

        if (!$this->fieldAclHelper->isFieldAclEnabled($class)) {
            return false;
        }

        $isFieldViewGranted = $this->fieldAclHelper->isFieldViewGranted($class, $name);

        $this->cache[$class][$name] = $isFieldViewGranted;

        return !$isFieldViewGranted;
    }
}
