<?php

namespace Alchemy\RestProvider\Tests\Middleware;

use Alchemy\RestProvider\Middleware\SetTransformAttributeMiddlewareFactory;
use Symfony\Component\HttpFoundation\Request;

class SetTransformAttributeMiddlewareFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testMiddlewareSetsRestAttributes()
    {
        $factory = new SetTransformAttributeMiddlewareFactory();
        $request = new Request();

        $middleware = $factory('test', true);
        $middleware($request);

        $this->assertTrue($request->attributes->has('_rest'));
        $this->assertEquals('test', $request->attributes->get('_rest[transform]', false, true));
        $this->assertEquals(true, $request->attributes->get('_rest[list]', false, true));
    }
}
