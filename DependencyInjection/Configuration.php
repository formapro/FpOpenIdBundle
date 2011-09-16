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
                        ->scalarNode('router_service')->cannotBeEmpty()->defaultValue('router')->end()
                        ->scalarNode('light_open_id_service')->cannotBeEmpty()->defaultValue('openid.light_open_id')->end()
                        ->scalarNode('token_persister_service')->cannotBeEmpty()->defaultValue('security.authentication.token_persister')->end()

                        ->scalarNode('return_route')->cannotBeEmpty()->end()
                        ->scalarNode('approve_route')->defaultNull()->end()
                        ->arrayNode('roles')
                            ->requiresAtLeastOneElement()
                            ->useAttributeAsKey('name')
                            ->prototype('scalar')->end()
                        ->end()
                        ->arrayNode('options_required')
                            ->addDefaultsIfNotSet()
                            ->useAttributeAsKey('name')
                            ->prototype('scalar')->end()
                         ->end()
                        ->arrayNode('options_optional')
                            ->addDefaultsIfNotSet()
                            ->useAttributeAsKey('name')
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('light_open_id')->isRequired()->cannotBeEmpty()
                    ->children()
                        ->scalarNode('trust_root')->cannotBeEmpty()->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}