<?php

namespace Alchemy\RestBundle\Tests\EventListener;

use Alchemy\RestBundle\EventListener\DateParamRequestListener;
use Prophecy\Argument;
use Symfony\Component\HttpKernel\KernelEvents;

class DateParamRequestListenerTest extends ListenerTest
{

    public function testListenerSubscribesToKernelRequestEvents()
    {
        $this->assertArrayHasKey(KernelEvents::REQUEST, DateParamRequestListener::getSubscribedEvents());
    }

    public function testRequestsWithoutDateAttributesAreIgnored()
    {
        $event = $this->getResponseEvent();
        $listener = new DateParamRequestListener();

        $listener->onKernelRequest($event);

        $this->assertEmpty($event->getRequest()->attributes->all());
    }

    public function testRequestsWithDateAttributesAreUpdated()
    {
        $date = '2015-08-01 12:00:00';
        $parsedDate = new \DateTime();

        $parser = $this->prophesize('Alchemy\Rest\Request\DateParser');
        $parser->parseDate(Argument::exact($date))->willReturn($parsedDate);

        $event = $this->getResponseEvent();
        $listener = new DateParamRequestListener($parser->reveal());

        $event->getRequest()->attributes->set('_dates', array('date'));
        $event->getRequest()->query->set('date', $date);

        $listener->onKernelRequest($event);

        $this->assertTrue($event->getRequest()->attributes->has('date'));
        $this->assertEquals($parsedDate, $event->getRequest()->attributes->get('date', null));
    }
}
