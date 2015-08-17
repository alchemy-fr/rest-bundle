<?php

namespace Alchemy\RestProvider\Tests\Middleware;

use Alchemy\RestProvider\Middleware\SetPaginationAndSortAttributesMiddlewareFactory;
use Symfony\Component\HttpFoundation\Request;

class SetPaginationAndSortAttributesMiddlewarefactoryFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testMiddlewareSetRequestAttributes()
    {
        $factory = new SetPaginationAndSortAttributesMiddlewareFactory();
        $request = new Request();

        $middleware = $factory(true, true);
        $middleware($request);

        $this->assertTrue($request->attributes->has('_sort'));
        $this->assertTrue($request->attributes->get('_sort', false));

        $this->assertTrue($request->attributes->has('_paginate'));
        $this->assertTrue($request->attributes->get('_paginate', false));
    }
}
