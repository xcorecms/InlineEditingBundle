<?php
declare(strict_types=1);

namespace XcoreCMS\InlineEditingBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
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
        $treeBuilder = new TreeBuilder();

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->root('xcore_inline');

        $children = $rootNode->children();
        $children->scalarNode('fallback')->defaultValue(false)->end();
        $children->scalarNode('table_name')->defaultValue('inline_content')->end();
        $children->scalarNode('url_path')->defaultValue('/inline-editing')->end();
        $children->scalarNode('connection')->defaultValue('doctrine.dbal.default_connection')->end();
        $children->scalarNode('entity_manager')->defaultValue('doctrine.orm.entity_manager')->end();
        $children->end();

        return $treeBuilder;
    }
}
