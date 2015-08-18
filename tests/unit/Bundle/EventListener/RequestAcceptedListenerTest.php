<?php

namespace Alchemy\RestBundle\Tests\EventListener;

use Alchemy\Rest\Result\RequestAcceptedResult;
use Alchemy\RestBundle\EventListener\RequestAcceptedListener;

class RequestAcceptedListenerTest extends GetControllerResultTest
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
}
