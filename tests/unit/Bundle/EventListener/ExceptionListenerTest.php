<?php

namespace Alchemy\RestBundle\Tests\EventListener;

use Alchemy\Rest\Request\ContentTypeMatcher;
use Alchemy\RestBundle\EventListener\ExceptionListener;
use Prophecy\Argument;

class ExceptionListenerTest extends GetControllerResultTest
{

    public function testExceptionsAreConvertedToHttpResponses()
    {
        $transformer = $this->prophesize('Alchemy\Rest\Response\ExceptionTransformer');
        $exception = new \Exception('test', 12);

        $transformer->transformException(Argument::exact($exception))->willReturn(array('test' => true));

        $listener = new ExceptionListener(new ContentTypeMatcher(), $transformer->reveal());
        $event = $this->getControllerExceptionEvent($exception);

        $listener->onKernelException($event);

        $this->assertHttpJsonResponse($event->getResponse(), 500, array('test' => true));
    }

    public function testListenerIgnoresUnmatchedContentTypes()
    {
        $transformer = $this->prophesize('Alchemy\Rest\Response\ExceptionTransformer');
        $exception = new \Exception('test', 12);
        $listener = new ExceptionListener(new ContentTypeMatcher(), $transformer->reveal(), array('text/plain'));

        $event = $this->getControllerExceptionEvent($exception);
        $event->getRequest()->headers->set('Accept', 'application/json');

        $listener->onKernelException($event);

        $this->assertNull($event->getResponse());
    }
}
