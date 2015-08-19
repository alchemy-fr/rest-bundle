<?php

namespace Alchemy\RestBundle\Tests\EventListener;

use Alchemy\Rest\Result\BadRequestResult;
use Alchemy\Rest\Result\ResourceCreatedResult;
use Alchemy\RestBundle\EventListener\ResourceCreatedListener;

class ResourceCreatedListenerTest extends ListenerTest
{

    public function testResourceCreatedResultsAreConvertedToJsonResponses()
    {
        $transformer = $this->prophesize('Alchemy\Rest\Response\ArrayTransformer');
        $resource = new \stdClass();
        $event = $this->getControllerResultEvent(new ResourceCreatedResult($resource));

        $listener = new ResourceCreatedListener($transformer->reveal());

        $listener->onKernelView($event);

        $this->assertHttpJsonResponse($event->getControllerResult(), 201, array());
    }

    public function testListenerOnlyConvertsSuccessResults()
    {
        $transformer = $this->prophesize('Alchemy\Rest\Response\ArrayTransformer');
        $result = new BadRequestResult();
        $event = $this->getControllerResultEvent($result);

        $listener = new ResourceCreatedListener($transformer->reveal());

        $listener->onKernelView($event);

        $this->assertSame($result, $event->getControllerResult());
        $this->assertNull($event->getResponse());
    }
}
