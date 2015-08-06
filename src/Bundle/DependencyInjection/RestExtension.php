<?php

namespace Alchemy\RestBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class RestExtension extends ConfigurableExtension
{
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new RestConfiguration();
    }

    protected function loadInternal(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(
            __DIR__ . '/../Resources/config'
        ));

        $loader->load('services.yml');

        $this->configureExceptionListener($config['exceptions'], $container);
        $this->configureRequestListener($config, $container);
    }

    protected function configureExceptionListener(array $config, ContainerBuilder $container)
    {
        if (! $config['exceptions']['enabled']) {
            $container->removeDefinition('alchemy_rest.exception_listener');

            return;
        }

        $listenerDefinition = $container->getDefinition('alchemy_rest.exception_listener');

        if ($config['exceptions']['transformer'] !== null) {
            $listenerDefinition->replaceArgument(0, new Reference($config['exceptions']['transformer']));
        }

        $listenerDefinition->replaceArgument(1, $config['exceptions']['content_types']);
    }

    protected function configureRequestListener(array $config, ContainerBuilder $container)
    {
        if (! $config['dates']['enabled']) {
            $container->removeDefinition('alchemy_rest.request_listener');

            return;
        }

        $dateParserDefinition = $container->getDefinition('alchemy_rest.date_parser');

        $dateParserDefinition->replaceArgument(0, $config['dates']['timezone']);
        $dateParserDefinition->replaceArgument(1, $config['dates']['format']);
    }
}
