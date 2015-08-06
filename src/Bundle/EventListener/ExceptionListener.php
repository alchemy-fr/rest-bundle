<?php

namespace Alchemy\RestBundle\EventListener;

use Alchemy\Rest\Response\ExceptionTransformer;
use Negotiation\Negotiator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
     * @var Negotiator
     */
    private $contentNegotiator;

    /**
     * @var ExceptionTransformer
     */
    private $exceptionTransformer;

    /**
     * @param ExceptionTransformer $exceptionTransformer
     * @param array $handledContentTypes
     */
    public function __construct(ExceptionTransformer $exceptionTransformer = null, array $handledContentTypes = null)
    {
        $this->contentNegotiator = new Negotiator();
        $this->exceptionTransformer = $exceptionTransformer ?: new ExceptionTransformer\DefaultExceptionTransformer();
        $this->handledContentTypes = $handledContentTypes ?: array('application/json');
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if (! $this->shouldHandleException($event->getRequest())) {
            return;
        }

        $exception = $event->getException();
        $data = $this->exceptionTransformer->transformException($exception);
        $status = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;

        $event->setResponse(new JsonResponse($data, $status));
    }

    /**
     * @param Request $request
     * @return bool
     */
    private function shouldHandleException(Request $request)
    {
        $header = $request->headers->get('Accept', '*/*');
        $format = $this->contentNegotiator->getBest($header, $this->handledContentTypes);

        if ($format && ! in_array($format->getValue(), $this->handledContentTypes, true)) {
            return false;
        }

        return true;
    }

    public static function getSubscribedEvents()
    {
        return array(KernelEvents::EXCEPTION => 'onKernelException');
    }
}
