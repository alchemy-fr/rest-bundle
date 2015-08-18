<?php

namespace Alchemy\RestBundle\Tests\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

abstract class GetControllerResultTest extends \PHPUnit_Framework_TestCase
{

    protected function getControllerResultEvent($result)
    {
        $kernel = $this->prophesize('Symfony\Component\HttpKernel\HttpKernelInterface');
        $request = new Request();

        return new GetResponseForControllerResultEvent(
            $kernel->reveal(),
            $request, HttpKernelInterface::MASTER_REQUEST,
            $result
        );
    }

    protected function assertHttpResponse($result, $statusCode)
    {
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $result);
        $this->assertEquals($statusCode, $result->getStatusCode());
    }

    protected function assertHttpJsonResponse($result, $statusCode, array $data)
    {
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\JsonResponse', $result);
        $this->assertEquals($statusCode, $result->getStatusCode());
        $this->assertEquals($data, json_decode($result->getContent(), true));
    }
}
