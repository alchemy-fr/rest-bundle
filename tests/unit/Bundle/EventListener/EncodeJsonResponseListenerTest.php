<?php

/*
 * This file is part of alchemy/pipeline-component.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\RestBundle\Tests\EventListener;

use Alchemy\RestBundle\EventListener\EncodeJsonResponseListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class EncodeJsonResponseListenerTest extends ListenerTest
{

    public function testListenerSubscribesToKernelViewEvents()
    {
        $this->assertArrayHasKey(KernelEvents::VIEW, EncodeJsonResponseListener::getSubscribedEvents());
    }

    public function testListenerIgnoresRequestsWhenFeatureIsDisabled()
    {
        $kernel = $this->prophesize('\Symfony\Component\HttpKernel\HttpKernelInterface')->reveal();
        $request = new Request();
        $event = new GetResponseForControllerResultEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, [ 'my data' ]);

        $listener = new EncodeJsonResponseListener();

        $listener->onKernelView($event);

        $this->assertEquals([ 'my data' ], $event->getControllerResult(), 'Listener should not modify controller result');
        $this->assertNull($event->getResponse(), 'Listener should not have set a response.');
    }

    public function testListenerTriggersErrorWhenControllerResultIsNotAnArray()
    {
        $kernel = $this->prophesize('\Symfony\Component\HttpKernel\HttpKernelInterface')->reveal();

        $request = new Request();
        $request->attributes->set('_rest', [ 'encode_response' => true ]);

        $event = new GetResponseForControllerResultEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, 'my data');

        $listener = new EncodeJsonResponseListener();

        $this->setExpectedException('\LogicException');

        $listener->onKernelView($event);
    }

    public function testListenerEncodesWellFormedControllerResultToJsonResponse()
    {
        $kernel = $this->prophesize('\Symfony\Component\HttpKernel\HttpKernelInterface')->reveal();

        $request = new Request();
        $event = new GetResponseForControllerResultEvent($kernel, $request, HttpKernelInterface::MASTER_REQUEST, [ 'my data' ]);
        $listener = new EncodeJsonResponseListener();

        $request->attributes->set('_rest', [ 'encode_response' => true ]);

        $listener->onKernelView($event);

        $this->assertHttpJsonResponse($event->getResponse(), 200, [ 'my data' ]);
    }
}
