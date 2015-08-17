<?php

namespace Alchemy\RestBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class RestExtension extends ConfigurableExtension
{
    /**
     * @param array $config
     * @param ContainerBuilder $container
     * @return RestConfiguration
     */
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new RestConfiguration();
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     */
    protected function loadInternal(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(
            __DIR__ . '/../Resources/config'
        ));

        $loader->load('services.yml');
        $loader->load('listeners.yml');

        $this->configureExceptionListener($config['exceptions'], $container);
        $this->configureDateRequestListener($config, $container);
        $this->configurePaginationRequestListener($config, $container);
        $this->configureSortRequestListener($config, $container);
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     */
    protected function configureExceptionListener(array $config, ContainerBuilder $container)
    {
        if (! $config['enabled']) {
            $container->removeDefinition('alchemy_rest.exception_listener');

            return;
        }

        $listenerDefinition = $container->getDefinition('alchemy_rest.exception_listener');

        if ($config['transformer'] !== null) {
            $listenerDefinition->replaceArgument(0, new Reference($config['transformer']));
        }

        $listenerDefinition->replaceArgument(1, $config['content_types']);
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     */
    protected function configureDateRequestListener(array $config, ContainerBuilder $container)
    {
        if (! $config['dates']['enabled']) {
            $container->removeDefinition('alchemy_rest.date_request_listener');

            return;
        }

        $dateParser = $container->getDefinition('alchemy_rest.date_parser');

        $dateParser->setArguments(array(
            $config['dates']['timezone'],
            $config['dates']['format']
        ));
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     */
    protected function configureSortRequestListener(array $config, ContainerBuilder $container)
    {
        if (! $config['sort']['enabled']) {
            $container->removeDefinition('alchemy_rest.sort_request_listener');

            return;
        }

        $sortOptionsFactory = $container->getDefinition('alchemy_rest.sort_options_factory');
        $sortOptionsFactory->setArguments(array(
            $config['sort']['sort_parameter'],
            $config['sort']['direction_parameter'],
            $config['sort']['multi_sort_parameter']
        ));
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     */
    protected function configurePaginationRequestListener(array $config, ContainerBuilder $container)
    {
        if (! $config['pagination']['enabled']) {
            $container->removeDefinition('alchemy_rest.paginate_request_listener');

            return;
        }

        $paginateOptionsFactory = $container->getDefinition('alchemy_rest.paginate_options_factory');

        $paginateOptionsFactory->setArguments(array(
            $config['pagination']['offset_parameter'],
            $config['pagination']['limit_parameter']
        ));
    }
}
