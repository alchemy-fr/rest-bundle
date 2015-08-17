<?php

namespace Alchemy\RestProvider\Tests\Middleware;

use Alchemy\RestProvider\Middleware\SetEncodingAttributeMiddlewareFactory;
use Symfony\Component\HttpFoundation\Request;

class SetEncodingAttributeMiddlewareFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testGeneratedMiddlewareSetsRestAttributes()
    {
        $factory = new SetEncodingAttributeMiddlewareFactory();
        $request = new Request();

        $middleware = $factory(true, true);
        $middleware($request);

        $this->assertTrue($request->attributes->has('_rest'));
        $this->assertTrue($request->attributes->get('_rest[encode_response]', false, true));
        $this->assertTrue($request->attributes->get('_rest[decode_request]', false, true));
    }

    public function testGeneratedMiddlewareUpdatesRestAttributesWhenAlreadySet()
    {
        $factory = new SetEncodingAttributeMiddlewareFactory();
        $request = new Request();

        $request->attributes->set('_rest', array('beacon' => true));

        $middleware = $factory(true, true);
        $middleware($request);

        $this->assertTrue($request->attributes->has('_rest'));
        $this->assertTrue($request->attributes->get('_rest[beacon]', false, true));
        $this->assertTrue($request->attributes->get('_rest[encode_response]', false, true));
        $this->assertTrue($request->attributes->get('_rest[decode_request]', false, true));
    }
}
