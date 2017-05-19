<?php
declare(strict_types=1);

namespace XcoreCMS\InlineEditingBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class XcoreCMSInlineEditingExtension extends Extension
{
    /**
     * @param array $configs An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('xcore_inline.fallback', $config['fallback']);
        $container->setParameter('xcore_inline.table_name', $config['table_name']);
        $container->setParameter('xcore_inline.url_path', $config['url_path']);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return 'xcore_inline';
    }
}
