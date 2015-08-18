<?php

namespace Alchemy\RestBundle\Tests\EventListener;

use Alchemy\Rest\Result\BadRequestResult;
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
}
