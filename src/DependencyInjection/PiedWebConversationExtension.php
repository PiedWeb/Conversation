<?php

namespace PiedWeb\ConversationBundle\DependencyInjection;

use PiedWeb\CMSBundle\DependencyInjection\PiedWebCMSExtension as Extension;
use Symfony\Component\Config\FileLocator;
//use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class PiedWebConversationExtension extends Extension //implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        self::loadConfigToParameters($container, $config, 'conversation.');

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
    }

    public function getAlias()
    {
        return 'piedweb_conversation';
    }
}
