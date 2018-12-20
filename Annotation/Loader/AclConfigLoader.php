<?php

namespace Pintushi\Bundle\SecurityBundle\Annotation\Loader;

use Pintushi\Bundle\SecurityBundle\Annotation\Acl as AclAnnotation;
use Pintushi\Bundle\SecurityBundle\DependencyInjection\PintushiSecurityExtension;
use Pintushi\Bundle\SecurityBundle\Metadata\AclAnnotationStorage;

class AclConfigLoader implements AclAnnotationLoaderInterface
{
    /**
     * Loads ACL annotations from config files
     *
     * @param AclAnnotationStorage $storage
     */
    public function load(AclAnnotationStorage $storage)
    {
        $configLoader = PintushiSecurityExtension::getAclConfigLoader();
        $resources = $configLoader->load();
        $root = PintushiSecurityExtension::ACLS_CONFIG_ROOT_NODE;

        foreach ($resources as $resource) {
            if (array_key_exists($root, $resource->data) && is_array($resource->data[$root])) {
                foreach ($resource->data[$root] as $id => $data) {
                    $data['id'] = $id;
                    $storage->add(new AclAnnotation($data));
                    if (isset($data['bindings'])) {
                        foreach ($data['bindings'] as $binding) {
                            $storage->addBinding(
                                $id,
                                isset($binding['class']) ? $binding['class'] : null,
                                isset($binding['method']) ? $binding['method'] : null
                            );
                        }
                    }
                }
            }
        }
    }
}
