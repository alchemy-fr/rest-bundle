<?php

namespace Alchemy\RestBundle\Tests\EventListener;

use Alchemy\Rest\Result\BadRequestResult;
use Alchemy\Rest\Result\SuccessResult;
use Alchemy\RestBundle\EventListener\BadRequestListener;
use Symfony\Component\HttpFoundation\Response;

class BadRequestListenerTest extends GetControllerResultTest
{
    public function testBadRequestResultsAreConvertedToHttpResponses()
    {
        $event = $this->getControllerResultEvent(new BadRequestResult());
        $listener = new BadRequestListener();

        $listener->onKernelView($event);

        /** @var Response $result */
        $result = $event->getControllerResult();

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\Response', $result);
        $this->assertEquals(400, $result->getStatusCode());
    }

    public function testOnlyBadRequestResultsAreTransformed()
    {
        $event = $this->getControllerResultEvent(new SuccessResult());
        $listener = new BadRequestListener();

        $listener->onKernelView($event);

        $this->assertInstanceOf('Alchemy\Rest\Result\SuccessResult', $event->getControllerResult());
    }
}
