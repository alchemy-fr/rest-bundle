<?php

namespace Alchemy\RestBundle\Tests\EventListener;

use Alchemy\Rest\Result\BadRequestResult;
use Alchemy\Rest\Result\SuccessResult;
use Alchemy\RestBundle\EventListener\BadRequestListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;

class BadRequestListenerTest extends ListenerTest
{

    public function testListenerSubscribesToKernelViewEvents()
    {
        $this->assertArrayHasKey(KernelEvents::VIEW, BadRequestListener::getSubscribedEvents());
    }

    public function testBadRequestResultsAreConvertedToHttpResponses()
    {
        $event = $this->getControllerResultEvent(new BadRequestResult());
        $listener = new BadRequestListener();

        $listener->onKernelView($event);

        /** @var Response $result */
        $result = $event->getControllerResult();

        $this->assertHttpResponse($result, 400);
    }

    public function testOnlyBadRequestResultsAreTransformed()
    {
        $event = $this->getControllerResultEvent(new SuccessResult());
        $listener = new BadRequestListener();

        $listener->onKernelView($event);

        $this->assertInstanceOf('Alchemy\Rest\Result\SuccessResult', $event->getControllerResult());
    }
}
