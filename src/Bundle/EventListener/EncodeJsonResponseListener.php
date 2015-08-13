<?php

namespace Alchemy\RestBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class EncodeJsonResponseListener implements EventSubscriberInterface
{

    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        if (! $event->getRequest()->attributes->get('_rest[encode_response]', false, true)) {
            return;
        }

        $result = $event->getControllerResult();

        if (! is_array($result)) {
            throw new \LogicException('Invalid controller result: array was expected.');
        }

        $event->setResponse(new JsonResponse($result));
    }

    public static function getSubscribedEvents()
    {
        return array(KernelEvents::VIEW => 'onKernelView');
    }
}
