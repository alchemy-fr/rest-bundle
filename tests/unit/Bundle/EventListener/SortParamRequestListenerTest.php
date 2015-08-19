<?php

namespace Alchemy\RestBundle\Tests\EventListener;

use Alchemy\RestBundle\EventListener\SortParamRequestListener;
use Alchemy\RestBundle\Rest\Request\SortOptionsFactory;

class SortParamRequestListenerTest extends ListenerTest
{

    public function testRequestsWithoutSortAttributeAreIgnored()
    {
        $event = $this->getResponseEvent();
        $listener = new SortParamRequestListener(new SortOptionsFactory('sort', 'dir', 'sorts'));

        $listener->onKernelRequest($event);

        $this->assertFalse($event->getRequest()->attributes->has('sort'));
    }

    public function testRequestsWithSortAttributeAreUpdated()
    {
        $event = $this->getResponseEvent();
        $listener = new SortParamRequestListener(new SortOptionsFactory('sort', 'dir', 'sorts'));

        $event->getRequest()->attributes->set('_sort', true);
        $event->getRequest()->query->add(array('sort' => 'property', 'dir' => 'desc'));

        $listener->onKernelRequest($event);

        $sort = $event->getRequest()->attributes->get('sort', null);

        $this->assertNotNull($sort);
        $this->assertInstanceOf('Alchemy\Rest\Request\SortOptions', $sort);
    }
}
