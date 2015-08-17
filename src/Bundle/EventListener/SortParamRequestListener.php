<?php

namespace Alchemy\RestBundle\EventListener;

use Alchemy\RestBundle\Rest\Request\SortOptionsFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SortParamRequestListener implements EventSubscriberInterface
{
    /**
     * @var SortOptionsFactory
     */
    private $factory;

    /**
     * @param SortOptionsFactory $factory
     */
    public function __construct(SortOptionsFactory $factory)
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

        if (($config = $attributes->get('_sort', false)) === false) {
            return;
        }

        if (! is_array($config)) {
            return;
        }

        $request->attributes->set('sort', $this->factory->create($request->query->all(), $config));
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array('onKernelRequest', -1)
        );
    }
}
