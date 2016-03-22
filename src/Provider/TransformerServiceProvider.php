<?php

namespace Alchemy\RestProvider;

use Alchemy\Rest\Response\ArrayTransformer;
use Alchemy\RestBundle\EventListener;
use League\Fractal\Manager;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

class TransformerServiceProvider implements ServiceProviderInterface
{

    private $subscriberKeys = [];

    public function register(Application $app)
    {
        $this->registerControllerResultListeners($app);
        $this->registerResponseListener($app);

        $this->registerRequestDecoder($app);
        $this->registerResponseEncoder($app);

        $app['dispatcher'] = $app->share(
            $app->extend('dispatcher', function (EventDispatcherInterface $dispatcher) use ($app) {
                $this->bindResultListeners($app, $dispatcher);

                return $dispatcher;
            })
        );
    }

    private function shareEventListener(Application $app, $key, callable $factory)
    {
        $app[$key] = $app->share($factory);

        $this->subscriberKeys[] = $key;
    }

    private function bindResultListeners(Application $app, EventDispatcherInterface $dispatcher)
    {
        foreach ($this->subscriberKeys as $subscriberKey) {
            $dispatcher->addSubscriber($app[$subscriberKey]);
        }
    }

    public function boot(Application $app)
    {
        // no-op
    }

    /**
     * @param Application $app
     * @return Application
     */
    private function registerRequestDecoder(Application $app)
    {
        $app['alchemy_rest.decode_request_content_types'] = array('application/json');

        $app['alchemy_rest.decode_request_listener'] = $app->share(function () use ($app) {
            return new EventListener\DecodeJsonBodyRequestListener($app['alchemy_rest.content_type_matcher']);
        });

        $app['alchemy_rest.request_decoder'] = $app->protect(function (Request $request) use ($app) {
            $app['alchemy_rest.decode_request_listener']->decodeBody($request);
        });
    }

    /**
     * @param Application $app
     * @return Application
     */
    private function registerResponseEncoder(Application $app)
    {
        $app['alchemy_rest.encode_response_listener'] = $app->share(function () use ($app) {
            return new EventListener\EncodeJsonResponseListener();
        });
    }

    /**
     * @param Application $app
     * @return Application
     */
    private function registerResponseListener(Application $app)
    {
        $app['alchemy_rest.fractal_manager'] = $app->share(function () {
            return new Manager();
        });

        $app['alchemy_rest.transformers_registry'] = $app->share(function () {
            return new \Pimple();
        });

        $app['alchemy_rest.array_transformer'] = $app->share(function () use ($app) {
            return new ArrayTransformer(
                $app['alchemy_rest.fractal_manager'],
                $app['alchemy_rest.transformers_registry']
            );
        });

        $app['alchemy_rest.transform_response_listener'] = $app->share(function () use ($app) {
            return new EventListener\TransformResponseListener(
                $app['alchemy_rest.array_transformer'],
                $app['url_generator']
            );
        });
    }

    /**
     * @param Application $app
     */
    private function registerControllerResultListeners(Application $app)
    {
        $this->shareEventListener($app, 'alchemy_rest.transform_success_result_listener', function () {
            return new EventListener\SuccessResultListener();
        });

        $this->shareEventListener($app, 'alchemy_rest.transform_bad_request_listener', function () {
            return new EventListener\BadRequestListener();
        });

        $this->shareEventListener($app, 'alchemy_rest.transform_request_accepted_listener', function () {
            return new EventListener\RequestAcceptedListener();
        });

        $this->shareEventListener($app, 'alchemy_rest.transform_resource_created_listener', function () use ($app) {
            return new EventListener\ResourceCreatedListener($app['alchemy_rest.array_transformer']);
        });
    }
}
