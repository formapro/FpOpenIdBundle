<?php
namespace Fp\OpenIdBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * 
     *  {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('fp_open_id');

        $rootNode
            ->children()
                ->arrayNode('provider')->isRequired()->cannotBeEmpty()
                    ->children()
                        ->scalarNode('return_route')->cannotBeEmpty()->end()
                        ->scalarNode('approve_route')->defaultNull()->end()
                        ->arrayNode('roles')
                            ->requiresAtLeastOneElement()
                            ->useAttributeAsKey('name')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('consumers')
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->arrayNode('required')
                                ->addDefaultsIfNotSet()
                                ->useAttributeAsKey('name')
                                ->prototype('scalar')->end()
                            ->end()
                            ->arrayNode('optional')
                                ->addDefaultsIfNotSet()
                                ->useAttributeAsKey('name')
                                ->prototype('scalar')->end()
                            ->end()
                            ->scalarNode('trust_root')->defaultValue('from_request')->cannotBeEmpty()->end()
                            ->scalarNode('default')->defaultFalse()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}