<?php

namespace Alchemy\RestBundle\Tests\EventListener;

use Alchemy\RestBundle\EventListener\PaginationParamRequestListener;
use Alchemy\RestBundle\Rest\Request\PaginationOptionsFactory;
use Symfony\Component\HttpKernel\KernelEvents;

class PaginationParamRequestListenerTest extends ListenerTest
{

    public function testListenerSubscribesToKernelRequestEvents()
    {
        $this->assertArrayHasKey(KernelEvents::REQUEST, PaginationParamRequestListener::getSubscribedEvents());
    }

    public function testRequestsWithoutPaginateAttributeAreIgnored()
    {
        $event = $this->getResponseEvent();
        $listener = new PaginationParamRequestListener(new PaginationOptionsFactory('offset', 'limit'));

        $listener->onKernelRequest($event);

        $this->assertFalse($event->getRequest()->attributes->has('pagination'));
    }

    public function testRequestsWithPaginateAttributeAreUpdated()
    {
        $event = $this->getResponseEvent();
        $listener = new PaginationParamRequestListener(new PaginationOptionsFactory('offset', 'limit'));

        $event->getRequest()->attributes->set('_paginate', true);

        $listener->onKernelRequest($event);

        $pagination = $event->getRequest()->attributes->get('pagination', null);

        $this->assertNotNull($pagination);
        $this->assertInstanceOf('Alchemy\Rest\Request\PaginationOptions', $pagination);
    }
}
