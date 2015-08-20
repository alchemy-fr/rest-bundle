<?php

namespace Alchemy\RestBundle\Tests\EventListener;

use Alchemy\Rest\Request\ContentTypeMatcher;
use Alchemy\RestBundle\EventListener\DecodeJsonBodyRequestListener;

class DecodeJsonBodyRequestListenerTest extends ListenerTest
{

    public function testRequestBodyIsNotDecodedWhenDecodeAttributeIsNotSet()
    {
        $event = $this->getResponseEvent();
        $listener = new DecodeJsonBodyRequestListener(new ContentTypeMatcher());

        $event->getRequest()->request->replace(array('beacon' => true));

        $listener->onKernelRequest($event);

        $this->assertEquals(array('beacon' => true), $event->getRequest()->request->all());
    }
}
