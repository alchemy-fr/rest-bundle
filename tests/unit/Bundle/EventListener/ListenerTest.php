<?php

namespace Alchemy\RestBundle\Tests\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

abstract class ListenerTest extends \PHPUnit\Framework\TestCase
{
    use \Prophecy\PhpUnit\ProphecyTrait;


    protected function getControllerResultEvent($result)
    {
        $kernel = $this->prophesize('Symfony\Component\HttpKernel\HttpKernelInterface');
        $request = new Request();

        return new ViewEvent(
            $kernel->reveal(),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $result
        );
    }

    protected function getControllerExceptionEvent(\Exception $exception)
    {
        $kernel = $this->prophesize('Symfony\Component\HttpKernel\HttpKernelInterface');
        $request = new Request();

        return new ExceptionEvent(
            $kernel->reveal(),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $exception
        );
    }

    protected function getResponseEvent($content = null)
    {
        $kernel = $this->prophesize('Symfony\Component\HttpKernel\HttpKernelInterface');
        $request = new Request(array(), array(), array(), array(), array(), array(), $content);

        return new RequestEvent(
            $kernel->reveal(),
            $request,
            HttpKernelInterface::MAIN_REQUEST
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
