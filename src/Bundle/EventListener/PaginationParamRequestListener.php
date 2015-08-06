<?php

namespace Alchemy\RestBundle\EventListener;

use Alchemy\RestBundle\Request\PaginationOptionsFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class PaginationParamRequestListener implements EventSubscriberInterface
{
    /**
     * @var PaginationOptionsFactory
     */
    private $factory;

    /**
     * @param PaginationOptionsFactory $factory
     */
    public function __construct(PaginationOptionsFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $attributes = $request->attributes;

        if (($config = $attributes->get('_paginate', false)) === false) {
            return;
        }

        if (! is_array($config)) {
            $config = array();
        }

        $request->attributes->set(
            'pagination',
            $this->factory->create($request->query->all(), $config)
        );
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array('onKernelRequest')
        );
    }
}
