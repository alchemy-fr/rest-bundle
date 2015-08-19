<?php

namespace Alchemy\RestBundle\Tests\EventListener;

use Alchemy\Rest\Result\BadRequestResult;
use Alchemy\Rest\Result\RequestAcceptedResult;
use Alchemy\RestBundle\EventListener\RequestAcceptedListener;

class RequestAcceptedListenerTest extends ListenerTest
{

    public function testEmptyRequestAcceptedResultsAreConvertedToDemandAcceptedHttpResponses()
    {
        $event = $this->getControllerResultEvent(new RequestAcceptedResult());
        $listener = new RequestAcceptedListener();

        $listener->onKernelView($event);

        $this->assertHttpResponse($event->getControllerResult(), 202);
    }

    public function testRequestAcceptedResultsWithMetadataAreConvertedToDemandAcceptedHttpResponses()
    {
        $event = $this->getControllerResultEvent(new RequestAcceptedResult(array('test' => true)));
        $listener = new RequestAcceptedListener();

        $listener->onKernelView($event);

        $this->assertHttpJsonResponse($event->getControllerResult(), 202, array(
            'test' => true
        ));
    }

    public function testOnlyRequestAcceptedResultsAreTransformed()
    {
        $result = new BadRequestResult();
        $event = $this->getControllerResultEvent($result);
        $listener = new RequestAcceptedListener();

        $listener->onKernelView($event);

        $this->assertSame($result, $event->getControllerResult());
    }
}
