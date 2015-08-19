<?php

namespace Alchemy\RestBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestMatchingExceptionListener implements EventSubscriberInterface
{

    /**
     * @var ExceptionListener
     */
    private $exceptionListener;

    /**
     * @var RequestMatcherInterface
     */
    private $matcher;

    /**
     * @param ExceptionListener $listener
     * @param RequestMatcherInterface $matcher
     */
    public function __construct(ExceptionListener $listener, RequestMatcherInterface $matcher)
    {
        $this->exceptionListener = $listener;
        $this->matcher = $matcher;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if (! $this->matcher->matches($event->getRequest())) {
            return;
        }

        $this->exceptionListener->onKernelException($event);
    }

    public static function getSubscribedEvents()
    {
        return array(KernelEvents::EXCEPTION => 'onKernelException');
    }
}
