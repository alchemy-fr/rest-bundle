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
use Alchemy\Rest\Response\ArrayTransformer;
use Alchemy\Rest\Response\ExceptionTransformer\DefaultExceptionTransformer;
use Alchemy\RestBundle\EventListener\BadRequestListener;
use Alchemy\RestBundle\EventListener\DateParamRequestListener;
use Alchemy\RestBundle\EventListener\DecodeJsonBodyRequestListener;
use Alchemy\RestBundle\EventListener\EncodeJsonResponseListener;
use Alchemy\RestBundle\EventListener\ExceptionListener;
use Alchemy\RestBundle\EventListener\PaginationParamRequestListener;
use Alchemy\RestBundle\EventListener\RequestAcceptedListener;
use Alchemy\RestBundle\EventListener\ResourceCreatedListener;
use Alchemy\RestBundle\EventListener\SortParamRequestListener;
use Alchemy\RestBundle\EventListener\SuccessResultListener;
use Alchemy\RestBundle\EventListener\TransformResponseListener;
use Alchemy\RestBundle\Rest\Request\PaginationOptionsFactory;
use Alchemy\RestBundle\Rest\Request\SortOptionsFactory;
use Alchemy\RestProvider\Middleware\SetDateAttributesMiddlewareFactory;
use Alchemy\RestProvider\Middleware\SetEncodingAttributeMiddlewareFactory;
use Alchemy\RestProvider\Middleware\SetPaginationAndSortAttributesMiddlewareFactory;
use Alchemy\RestProvider\Middleware\SetTransformAttributeMiddlewareFactory;
use League\Fractal\Manager;
use Negotiation\Negotiator;
use Pimple;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
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
        $this->registerTransformListener($app);

        $app['alchemy_rest.decode_request_content_types'] = array('application/json');
        $app['alchemy_rest.decode_request_listener'] = $app->share(function () use ($app) {
            return new DecodeJsonBodyRequestListener($app['alchemy_rest.content_type_matcher']);
        });

        $app['alchemy_rest.request_decoder'] = $app->protect(function (Request $request) use ($app) {
            $app['alchemy_rest.decode_request_listener']->decodeBody($request);
        });

        $app['alchemy_rest.encode_response_listener'] = $app->share(function () use ($app) {
            return new EncodeJsonResponseListener();
        });

        $this->registerMiddlewareFactories($app);

        $app['dispatcher'] = $app->share(
            $app->extend('dispatcher', function (EventDispatcherInterface $dispatcher) use ($app) {
                $this->bindRequestListeners($app, $dispatcher);
                $this->bindResultListeners($app, $dispatcher);

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

    private function bindResultListeners(Application $app, EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addSubscriber($app['alchemy_rest.transform_success_result_listener']);
        $dispatcher->addSubscriber($app['alchemy_rest.transform_request_accepted_listener']);
        $dispatcher->addSubscriber($app['alchemy_rest.transform_resource_created_listener']);
        $dispatcher->addSubscriber($app['alchemy_rest.transform_bad_request_listener']);
    }

    public function boot(Application $app)
    {
        // Nothing to do.
    }

    private function registerMiddlewareFactories(Application $app)
    {
        $app['alchemy_rest.middleware.parse_date_params'] = $app->share(function () {
            return new SetDateAttributesMiddlewareFactory();
        });

        $app['alchemy_rest.middleware.parse_list_params'] = $app->share(function () {
            return new SetPaginationAndSortAttributesMiddlewareFactory();
        });

        $app['alchemy_rest.middleware.transform_response'] = $app->share(function () {
            return new SetTransformAttributeMiddlewareFactory();
        });

        $app['alchemy_rest.middleware.json_encoder'] = $app->share(function () {
            return new SetEncodingAttributeMiddlewareFactory();
        });
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
            return new PaginationParamRequestListener($app['alchemy_rest.paginate_options_factory']);
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
            return new SortParamRequestListener($app['alchemy_rest.sort_options_factory']);
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
            return new DateParamRequestListener($app['alchemy_rest.date_parser']);
        });
    }

    private function registerExceptionListener(Application $app)
    {
        $app['alchemy_rest.exception_transformer'] = $app->share(function () use ($app) {
            return new DefaultExceptionTransformer($app['alchemy_rest.debug']);
        });

        $app['alchemy_rest.exception_handling_content_types'] = array('application/json');
        $app['alchemy_rest.exception_listener'] = $app->share(function () use ($app) {
            return new ExceptionListener(
                $app['alchemy_rest.content_type_matcher'],
                $app['alchemy_rest.exception_transformer'],
                $app['alchemy_rest.exception_handling_content_types']
            );
        });
    }

    private function registerTransformListener(Application $app)
    {
        $app['alchemy_rest.fractal_manager'] = $app->share(function () {
            return new Manager();
        });

        $app['alchemy_rest.transformers_registry'] = $app->share(function () {
            return new Pimple();
        });
        $app['alchemy_rest.array_transformer'] = $app->share(function () use ($app) {
            return new ArrayTransformer(
                $app['alchemy_rest.fractal_manager'],
                $app['alchemy_rest.transformers_registry']
            );
        });

        $app['alchemy_rest.transform_bad_request_listener'] = $app->share(function () {
            return new BadRequestListener();
        });

        $app['alchemy_rest.transform_request_accepted_listener'] = $app->share(function () {
            return new RequestAcceptedListener();
        });

        $app['alchemy_rest.transform_resource_created_listener'] = $app->share(function () use ($app) {
            return new ResourceCreatedListener($app['alchemy_rest.array_transformer']);
        });

        $app['alchemy_rest.transform_success_result_listener'] = $app->share(function () {
            return new SuccessResultListener();
        });

        $app['alchemy_rest.transform_response_listener'] = $app->share(function () use ($app) {
            return new TransformResponseListener(
                $app['alchemy_rest.array_transformer'],
                $app['url_generator']
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
