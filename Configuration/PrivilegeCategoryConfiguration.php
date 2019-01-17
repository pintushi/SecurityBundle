<?php

namespace Pintushi\Bundle\SecurityBundle\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class PrivilegeCategoryConfiguration implements ConfigurationInterface
{
    const ROOT_NODE_NAME = 'pintushi_privilege_categories';

    /**
     * @param array $configs
     * @return array
     */
    public function processConfiguration(array $configs)
    {
        $processor = new Processor();

        return $processor->processConfiguration($this, $configs);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $root = $builder->root(static::ROOT_NODE_NAME);
        $root->useAttributeAsKey('id')
            ->beforeNormalization()
            ->always(
                function ($configs) {
                    foreach ($configs as $id => &$config) {
                        if (!isset($config['label'])) {
                            $config['label'] = $id;
                        }
                    }

                    return $configs;
                }
            )
            ->end()
            ->prototype('array')
                ->children()
                    ->scalarNode('label')->end()
                    ->scalarNode('priority')->defaultValue(0)->end()
                    ->booleanNode('visible')->defaultValue(true)->end()
                    ->booleanNode('tab')->defaultValue(true)->end()
                ->end()
            ->end();

        return $builder;
    }
}
