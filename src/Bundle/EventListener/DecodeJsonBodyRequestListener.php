<?php

namespace Alchemy\RestBundle\EventListener;

use Alchemy\Rest\Request\ContentTypeMatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class DecodeJsonBodyRequestListener implements EventSubscriberInterface
{
    /**
     * @var ContentTypeMatcher
     */
    private $contentTypeMatcher;

    /**
     * @var string[]
     */
    private $contentTypes;

    /**
     * @param ContentTypeMatcher $contentTypeMatcher
     * @param null|string[] $contentTypes
     */
    public function __construct(ContentTypeMatcher $contentTypeMatcher, array $contentTypes = null)
    {
        $this->contentTypeMatcher = $contentTypeMatcher;
        $this->contentTypes = $contentTypes ?: array('application/json');
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $acceptHeader = $request->headers->get('Accept', '*/*');

        if (! $this->contentTypeMatcher->matches($acceptHeader, $this->contentTypes)) {
            return;
        }

        $jsonBody = $request->getContent(false);
        $decodedBody = json_decode($jsonBody, true);

        if ($decodedBody === null) {
            return;
        }

        $request->request->replace($decodedBody);
    }

    public static function getSubscribedEvents()
    {
        return array(KernelEvents::REQUEST => 'onKernelRequest');
    }
}
