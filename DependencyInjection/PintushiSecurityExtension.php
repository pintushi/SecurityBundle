<?php


namespace Pintushi\Bundle\SecurityBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Oro\Component\Config\Loader\CumulativeConfigLoader;
use Oro\Component\Config\Loader\YamlCumulativeFileLoader;
use Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle;

final class PintushiSecurityExtension extends Extension implements PrependExtensionInterface
{
    const ACLS_CONFIG_ROOT_NODE = 'acls';

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
    }

    /**
     * @return CumulativeConfigLoader
     */
    public static function getAclConfigLoader()
    {
        return new CumulativeConfigLoader(
            'pintushi_acl_config',
            new YamlCumulativeFileLoader('Resources/config/app/acls.yml')
        );
    }

     /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container): void
    {
        if (!$container->hasExtension('doctrine_cache')) {
            throw new \RuntimeException(sprintf('DoctrineCacheBundle is required, install it with `composer require "doctrine/doctrine-cache-bundle"`'));
        }

        $configs = $container->getExtensionConfig('pintushi_security');
        $cacheProvider = $configs[0]['cache_provider'];

        $container->prependExtensionConfig('doctrine_cache', [
            'providers'=> [
                'permission' => [
                    'type' => $cacheProvider,
                    'namespace' => 'pintushi_permission',
                ],
                'configurable_permission' => [
                    'type' => $cacheProvider,
                    'namespace' => 'pintushi_security_configurable_permission',
                ]
            ]
        ]);
    }
}
