<?php
/*
 * This file is part of Phraseanet
 *
 * (c) 2005-2015 Alchemy
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Alchemy\RestProvider\Tests;

use Alchemy\RestProvider\RestProvider;
use Silex\Application;

class RestProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testItIsAServiceProvider()
    {
        $sut = new RestProvider();

        $this->assertInstanceOf('Silex\ServiceProviderInterface', $sut);
    }

    public function testItCanBeRegisteredAndBooted()
    {
        $restProvider = new RestProvider();
        $app = new Application();

        $defaultServices = $app->keys();

        $app->register($restProvider);
        $app->boot();

        $definedByProvider = array_values(array_diff($app->keys(), $defaultServices));

        $expectedServices = [
            'alchemy_rest.debug',
            'alchemy_rest.fractal_manager',
            'alchemy_rest.transformers_registry',
            'alchemy_rest.array_transformer',
            'alchemy_rest.exception_transformer',
            'alchemy_rest.exception_listener',
            'alchemy_rest.date_parser.timezone',
            'alchemy_rest.date_parser.format',
            'alchemy_rest.date_parser',
            'alchemy_rest.date_request_listener',
            'alchemy_rest.paginate_options.offset_parameter',
            'alchemy_rest.paginate_options.limit_parameter',
            'alchemy_rest.paginate_options_factory',
            'alchemy_rest.paginate_request_listener',
            'alchemy_rest.sort_options.sort_parameter',
            'alchemy_rest.sort_options.direction_parameter',
            'alchemy_rest.sort_options.multi_sort_parameter',
            'alchemy_rest.sort_options_factory',
            'alchemy_rest.sort_request_listener',
            'alchemy_rest.transform_response_listener',
        ];

        $undefinedServices = array_diff($expectedServices, $definedByProvider);

        $this->assertEquals([], $undefinedServices);
    }
}
