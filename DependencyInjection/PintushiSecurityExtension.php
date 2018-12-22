<?php


namespace Pintushi\Bundle\SecurityBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

use Oro\Component\Config\Loader\CumulativeConfigLoader;
use Oro\Component\Config\Loader\YamlCumulativeFileLoader;

final class PintushiSecurityExtension extends Extension
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
}
