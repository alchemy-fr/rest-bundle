<?php

namespace Alchemy\RestProvider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class MiddlewareServiceProvider implements ServiceProviderInterface
{

    public function register(Application $app)
    {
        $app['alchemy_rest.middleware.parse_date_params'] = $app->share(function () {
            return new Middleware\SetDateAttributesMiddlewareFactory();
        });

        $app['alchemy_rest.middleware.parse_list_params'] = $app->share(function () {
            return new Middleware\SetPaginationAndSortAttributesMiddlewareFactory();
        });

        $app['alchemy_rest.middleware.transform_response'] = $app->share(function () {
            return new Middleware\SetTransformAttributeMiddlewareFactory();
        });

        $app['alchemy_rest.middleware.json_encoder'] = $app->share(function () {
            return new Middleware\SetEncodingAttributeMiddlewareFactory();
        });
    }

    public function boot(Application $app)
    {
        // no-op
    }
}
