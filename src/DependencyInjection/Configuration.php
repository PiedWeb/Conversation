<?php

namespace PiedWeb\ConversationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * php bin/console config:dump-reference PiedWebConversationBundle.
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('piedweb_conversation');
        $treeBuilder
            ->getRootNode()
                ->children()
                    ->scalarNode('entity_message')->defaultValue('PiedWeb\ConversationBundle\Entity\Message')->end()
                    ->scalarNode('notification_emailTo')->defaultNull()->end()
                    ->scalarNode('notification_interval')
                        ->defaultValue('P12H')
                        ->info("DateInterval's format")
                    ->end()
                    ->arrayNode('form')
                    ->end()
                    ->scalarNode('form_message')
                        ->defaultValue('PiedWeb\ConversationBundle\Form\MessageForm')
                    ->end()
                    ->scalarNode('form_ms-message')
                        ->defaultValue('PiedWeb\ConversationBundle\Form\MultiStepMessageForm')
                    ->end()
                    ->scalarNode('form_newsletter')
                        ->defaultValue('PiedWeb\ConversationBundle\Form\NewsletterForm')
                    ->end()
                ->end()
        ;

        return $treeBuilder;
    }
}
