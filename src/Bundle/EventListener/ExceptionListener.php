<?php

namespace Alchemy\RestBundle\EventListener;

use Alchemy\Rest\Request\ContentTypeMatcher;
use Alchemy\Rest\Response\ExceptionTransformer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionListener implements EventSubscriberInterface
{
    /**
     * @var array
     */
    private $handledContentTypes;

    /**
     * @var ContentTypeMatcher
     */
    private $contentTypeMatcher;

    /**
     * @var ExceptionTransformer
     */
    private $exceptionTransformer;

    /**
     * @param ContentTypeMatcher $matcher
     * @param ExceptionTransformer $exceptionTransformer
     * @param array $handledContentTypes
     */
    public function __construct(
        ContentTypeMatcher $matcher,
        ExceptionTransformer $exceptionTransformer = null,
        array $handledContentTypes = null
    ) {
        $this->contentTypeMatcher = $matcher;
        $this->exceptionTransformer = $exceptionTransformer ?: new ExceptionTransformer\DefaultExceptionTransformer();
        $this->handledContentTypes = $handledContentTypes ?: array('application/json');
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $request = $event->getRequest();
        $acceptHeader = $request->headers->get('Accept', '*/*');

        if (!$this->contentTypeMatcher->matches($acceptHeader, $this->handledContentTypes)) {
            return;
        }

        $exception = $event->getException();
        $data = $this->exceptionTransformer->transformException($exception);
        $status = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;

        $event->setResponse(new JsonResponse($data, $status));
    }

    public static function getSubscribedEvents()
    {
        return array(KernelEvents::EXCEPTION => 'onKernelException');
    }
}
