<?php

namespace Alchemy\RestBundle\EventListener;

use Alchemy\Rest\Result\RequestAcceptedResult;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestAcceptedListener implements EventSubscriberInterface
{

    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $result = $event->getControllerResult();

        if (! $result instanceof RequestAcceptedResult) {
            return;
        }

        $metadata = $result->getMetadata();
        $response = empty($metadata) ? new Response('', 202) : new JsonResponse($metadata, 202);

        $event->setControllerResult($response);
    }

    public static function getSubscribedEvents()
    {
        return array(KernelEvents::VIEW => 'onKernelView');
    }
}
