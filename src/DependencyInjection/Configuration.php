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
        $treeBuilder = new TreeBuilder('xcore_inline');

        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('xcore_inline');
        }

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
