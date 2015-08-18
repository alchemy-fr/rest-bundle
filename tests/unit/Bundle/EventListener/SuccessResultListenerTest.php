<?php

namespace Alchemy\RestBundle\Tests\EventListener;

use Alchemy\Rest\Result\BadRequestResult;
use Alchemy\Rest\Result\SuccessResult;
use Alchemy\RestBundle\EventListener\SuccessResultListener;

class SuccessResultListenerTest extends GetControllerResultTest
{

    public function testEmptySuccessResultsAreConvertedToHttpNoContentResponses()
    {
        $listener = new SuccessResultListener();
        $event = $this->getControllerResultEvent(new SuccessResult());

        $listener->onKernelView($event);

        $this->assertHttpResponse($event->getControllerResult(), 204);
    }

    public function testSuccessResultsAreConvertedToHttpOkResponses()
    {
        $listener = new SuccessResultListener();
        $event = $this->getControllerResultEvent(new SuccessResult(array('test' => true)));

        $listener->onKernelView($event);

        $this->assertHttpJsonResponse($event->getControllerResult(), 200, array('test' => true));
    }

    public function testListenerOnlyConvertsSuccessResults()
    {
        $listener = new SuccessResultListener();
        $result = new BadRequestResult();
        $event = $this->getControllerResultEvent($result);

        $listener->onKernelView($event);

        $this->assertSame($result, $event->getControllerResult());
        $this->assertNull($event->getResponse());
    }
}
