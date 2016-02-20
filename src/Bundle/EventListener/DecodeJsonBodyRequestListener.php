<?php

namespace Alchemy\RestBundle\EventListener;

use Alchemy\Rest\Request\ContentTypeMatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
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
        $restAttributes = $request->attributes->get('_rest', []);

        if (! isset($restAttributes['decode_request']) || ! $restAttributes['decode_request']) {
            return;
        }

        $this->decodeBody($request);
    }

    /**
     * @param Request $request
     */
    public function decodeBody(Request $request)
    {
        $acceptHeader = $request->headers->get('Content-Type', '*/*');

        if (!$this->contentTypeMatcher->matches($acceptHeader, $this->contentTypes)) {
            return;
        }

        $jsonBody = $request->getContent(false);
        $decodedBody = json_decode($jsonBody, true);

        if (! is_array($decodedBody)) {
            $decodedBody = array();
        }

        $request->request->replace($decodedBody);
    }

    public static function getSubscribedEvents()
    {
        return array(KernelEvents::REQUEST => array('onKernelRequest', -2048));
    }
}
