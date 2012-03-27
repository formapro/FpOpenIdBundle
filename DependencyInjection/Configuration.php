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
                ->scalarNode('db_driver')->defaultNull()->end()
                ->scalarNode('identity_class')->defaultNull()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
