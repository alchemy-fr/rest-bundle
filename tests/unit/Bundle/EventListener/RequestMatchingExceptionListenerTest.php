<?php

namespace Alchemy\RestBundle\Tests\EventListener;

use Alchemy\RestBundle\EventListener\RequestMatchingExceptionListener;
use Prophecy\Argument;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestMatchingExceptionListenerTest extends ListenerTest
{

    public function testListenerSubscribesToKernelExceptionEvents()
    {
        $this->assertArrayHasKey(KernelEvents::EXCEPTION, RequestMatchingExceptionListener::getSubscribedEvents());
    }

    public function testDecoratedExceptionListenerIsNotCalledWhenRequestDoesNotMatch()
    {
        $decoratedListener = $this->prophesize('Alchemy\RestBundle\EventListener\ExceptionListener');
        $matcher = $this->prophesize('Symfony\Component\HttpFoundation\RequestMatcherInterface');

        $decoratedListener->onKernelException(Argument::any())->shouldNotBeCalled();
        $matcher->matches(Argument::any())->willReturn(false);

        $event = $this->getControllerExceptionEvent(new \Exception());
        $listener = new RequestMatchingExceptionListener($decoratedListener->reveal(), $matcher->reveal());

        $listener->onKernelException($event);
    }

    public function testDecoratedExceptionIsCalledWhenRequestMatches()
    {
        $event = $this->getControllerExceptionEvent(new \Exception());

        $decoratedListener = $this->prophesize('Alchemy\RestBundle\EventListener\ExceptionListener');
        $matcher = $this->prophesize('Symfony\Component\HttpFoundation\RequestMatcherInterface');

        $decoratedListener->onKernelException(Argument::exact($event))->shouldBeCalled();
        $matcher->matches(Argument::any())->willReturn(true);

        $listener = new RequestMatchingExceptionListener($decoratedListener->reveal(), $matcher->reveal());

        $listener->onKernelException($event);
    }
}
