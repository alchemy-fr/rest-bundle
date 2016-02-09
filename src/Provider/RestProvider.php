<?php
/*
 * This file is part of Phraseanet
 *
 * (c) 2005-2015 Alchemy
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Alchemy\RestProvider;

use Alchemy\Rest\Request\ContentTypeMatcher;
use Alchemy\Rest\Request\DateParser\FormatDateParser;
use Alchemy\Rest\Response\ExceptionTransformer\DefaultExceptionTransformer;
use Alchemy\RestBundle\EventListener;
use Alchemy\RestBundle\Rest\Request\PaginationOptionsFactory;
use Alchemy\RestBundle\Rest\Request\SortOptionsFactory;
use Alchemy\RestProvider\Middleware;
use Negotiation\Negotiator;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

class RestProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['alchemy_rest.debug'] = false;

        $this->registerContentTypeMatcher($app);
        $this->registerExceptionListener($app);
        $this->registerDateListener($app);
        $this->registerPaginationListener($app);
        $this->registerSortListener($app);

        $app->register(new TransformerServiceProvider());
        $app->register(new MiddlewareServiceProvider());

        $app['dispatcher'] = $app->share(
            $app->extend('dispatcher', function (EventDispatcherInterface $dispatcher) use ($app) {
                $this->bindRequestListeners($app, $dispatcher);

                // Bind exception
                $dispatcher->addSubscriber($app['alchemy_rest.exception_listener']);
                // This block must be called after all other result listeners
                $dispatcher->addSubscriber($app['alchemy_rest.transform_response_listener']);
                $dispatcher->addSubscriber($app['alchemy_rest.encode_response_listener']);

                return $dispatcher;
            })
        );
    }

    private function bindRequestListeners(Application $app, EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addSubscriber($app['alchemy_rest.decode_request_listener']);
        $dispatcher->addSubscriber($app['alchemy_rest.paginate_request_listener']);
        $dispatcher->addSubscriber($app['alchemy_rest.sort_request_listener']);
        $dispatcher->addSubscriber($app['alchemy_rest.date_request_listener']);
    }

    public function boot(Application $app)
    {
        // Nothing to do.
    }

    private function registerPaginationListener(Application $app)
    {
        $app['alchemy_rest.paginate_options.offset_parameter'] = 'offset';
        $app['alchemy_rest.paginate_options.limit_parameter'] = 'limit';
        $app['alchemy_rest.paginate_options_factory'] = $app->share(function () use ($app) {
            return new PaginationOptionsFactory(
                $app['alchemy_rest.paginate_options.offset_parameter'],
                $app['alchemy_rest.paginate_options.limit_parameter']
            );
        });

        $app['alchemy_rest.paginate_request_listener'] = $app->share(function () use ($app) {
            return new EventListener\PaginationParamRequestListener($app['alchemy_rest.paginate_options_factory']);
        });
    }

    private function registerSortListener(Application $app)
    {
        $app['alchemy_rest.sort_options.sort_parameter'] = 'sort';
        $app['alchemy_rest.sort_options.direction_parameter'] = 'dir';
        $app['alchemy_rest.sort_options.multi_sort_parameter'] = 'sorts';
        $app['alchemy_rest.sort_options_factory'] = $app->share(function () use ($app) {
            return new SortOptionsFactory(
                $app['alchemy_rest.sort_options.sort_parameter'],
                $app['alchemy_rest.sort_options.direction_parameter'],
                $app['alchemy_rest.sort_options.multi_sort_parameter']
            );
        });

        $app['alchemy_rest.sort_request_listener'] = $app->share(function () use ($app) {
            return new EventListener\SortParamRequestListener($app['alchemy_rest.sort_options_factory']);
        });
    }

    private function registerDateListener(Application $app)
    {
        $app['alchemy_rest.date_parser.timezone'] = 'UTC';
        $app['alchemy_rest.date_parser.format'] = 'Y-m-d H:i:s';
        $app['alchemy_rest.date_parser'] = $app->share(function () use ($app) {
            return new FormatDateParser(
                $app['alchemy_rest.date_parser.timezone'],
                $app['alchemy_rest.date_parser.format']
            );
        });

        $app['alchemy_rest.date_request_listener'] = $app->share(function () use ($app) {
            return new EventListener\DateParamRequestListener($app['alchemy_rest.date_parser']);
        });
    }

    private function registerExceptionListener(Application $app)
    {
        $app['alchemy_rest.exception_transformer'] = $app->share(function () use ($app) {
            return new DefaultExceptionTransformer($app['alchemy_rest.debug']);
        });

        $app['alchemy_rest.exception_handling_content_types'] = array('application/json');
        $app['alchemy_rest.exception_listener'] = $app->share(function () use ($app) {
            return new EventListener\ExceptionListener(
                $app['alchemy_rest.content_type_matcher'],
                $app['alchemy_rest.exception_transformer'],
                $app['alchemy_rest.exception_handling_content_types']
            );
        });
    }

    private function registerContentTypeMatcher(Application $app)
    {
        $app['alchemy_rest.negotiator'] = $app->share(function () use ($app) {
            return new Negotiator();
        });
        $app['alchemy_rest.content_type_matcher'] = $app->share(function () use ($app) {
            return new ContentTypeMatcher($app['alchemy_rest.negotiator']);
        });
    }
}
