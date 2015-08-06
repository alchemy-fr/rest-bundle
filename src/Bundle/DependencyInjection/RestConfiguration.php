<?php

namespace Alchemy\RestBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class RestConfiguration implements ConfigurationInterface
{

    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();

        $builder
            ->root('alchemy_rest')
                ->children()
                    ->arrayNode('exceptions')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->arrayNode('content_types')
                                ->fixXmlConfig('content_type')
                                ->addDefaultChildrenIfNoneSet()
                                ->prototype('scalar')
                                    ->defaultValue('application/json')
                                ->end()
                            ->end()
                            ->scalarNode('enabled')->defaultTrue()->end()
                            ->scalarNode('transformer')->defaultNull()->end()
                        ->end()
                    ->end()
                    ->arrayNode('dates')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('enabled')->defaultTrue()->end()
                            ->scalarNode('format')->defaultValue('Y-m-d H:i:s')->end()
                            ->scalarNode('timezone')->defaultValue('UTC')->end()
                        ->end()
                    ->end()
                    ->arrayNode('pagination')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('enabled')->defaultTrue()->end()
                            ->scalarNode('limit_parameter')->defaultValue('limit')->end()
                            ->scalarNode('offset_parameter')->defaultValue('offset')->end()
                        ->end()
                    ->end()
                    ->arrayNode('sort')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('enabled')->defaultTrue()->end()
                            ->scalarNode('sort_parameter')->defaultValue('sort')->end()
                            ->scalarNode('direction_parameter')->defaultValue('dir')->end()
                            ->scalarNode('multi_sort_parameter')->defaultValue('sorts')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $builder;
    }
}
