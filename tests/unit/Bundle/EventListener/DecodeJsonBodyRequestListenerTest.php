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

    public function testRequestBodyIsDecodedWhenDecodeAttributeIsSet()
    {
        $event = $this->getResponseEvent(json_encode([ 'test' => true ]));
        $listener = new DecodeJsonBodyRequestListener(new ContentTypeMatcher());

        $event->getRequest()->attributes->set('_rest', array('decode_request' => true));
        $event->getRequest()->request->replace(array('beacon' => true));

        $listener->onKernelRequest($event);

        $this->assertEquals(array('test' => true), $event->getRequest()->request->all());
    }

    public function testRequestBodyIsNotDecodedWhenContentTypeIsSetButEmpty()
    {
        $event = $this->getResponseEvent(json_encode([ 'test' => true ]));
        $listener = new DecodeJsonBodyRequestListener(new ContentTypeMatcher());

        $event->getRequest()->attributes->set('_rest', array('decode_request' => true));
        $event->getRequest()->request->replace(array('beacon' => true));
        $event->getRequest()->headers->set('Content-Type', '', true);

        $listener->onKernelRequest($event);

        $this->assertEquals(array('beacon' => true), $event->getRequest()->request->all());
    }
}
