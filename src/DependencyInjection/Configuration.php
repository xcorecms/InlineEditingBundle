<?php
declare(strict_types=1);

namespace XcoreCMS\InlineEditingBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Jakub Janata <jakubjanata@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder;
        $rootNode = $treeBuilder->root('xcore_inline');

        $rootNode
            ->children()
            ->scalarNode('fallback')->defaultValue(false)->end()
            ->scalarNode('table_name')->defaultValue('inline_content')->end()
            ->scalarNode('url_path')->defaultValue('/inline-editing')->end()
            ->end();

        return $treeBuilder;
    }
}
