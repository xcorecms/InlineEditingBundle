<?php

declare(strict_types=1);

namespace XcoreCMS\InlineEditingBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class XcoreCMSInlineEditingExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('xcore_inline.fallback', $config['fallback']);
        $container->setParameter('xcore_inline.table_name', $config['table_name']);
        $container->setParameter('xcore_inline.url_path', $config['url_path']);
        $container->setParameter('xcore_inline.connection', $config['connection']);
        $container->setParameter('xcore_inline.entity_manager', $config['entity_manager']);
        $container->setParameter('xcore_inline.default_namespace', $config['default_namespace']);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $this->copyResourceDirectory();
    }

    /**
     *
     */
    private function copyResourceDirectory(): void
    {
        $targetDir = __DIR__ . '/../Resources/public';
        $originDir = __DIR__ . '/../../../inline-editing/client-side/dist';

        $filesystem = new Filesystem();
        $filesystem->mkdir($targetDir);
        $filesystem->mirror($originDir, $targetDir);
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return 'xcore_inline';
    }
}
