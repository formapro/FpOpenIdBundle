<?php
namespace Fp\OpenIdBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
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

        $this->addTemplateSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addTemplateSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('template')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('engine')->defaultValue('twig')->end()
                ->end()
            ->end()
        ->end();
    }
}
