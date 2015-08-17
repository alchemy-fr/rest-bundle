<?php

namespace Alchemy\RestProvider\Tests\Middleware;

use Alchemy\RestProvider\Middleware\SetDateAttributesMiddlewareFactory;
use Symfony\Component\HttpFoundation\Request;

class SetDateAttributesMiddlewareFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testGeneratedMiddlewareSetsDateAttribute()
    {
        $factory = new SetDateAttributesMiddlewareFactory();
        $request = new Request();

        $middleware = $factory(array('from', 'to'));
        $middleware($request);

        $this->assertEquals(array('from', 'to'), $request->attributes->get('_dates'));
    }
}
