<?php

namespace Alchemy\RestBundle\EventListener;

use Alchemy\Rest\Request\DateParser;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class DateParamRequestListener implements EventSubscriberInterface
{

    /**
     * @var DateParser
     */
    private $dateParser;

    /**
     * @param DateParser $parser
     */
    public function __construct(DateParser $parser = null)
    {
        $this->dateParser = $parser ?: new DateParser\FormatDateParser();
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $attributes = $request->attributes;

        if (! $attributes->has('_dates')) {
            return;
        }

        $dateKeys = $attributes->get('_dates', array(), true);

        foreach ($dateKeys as $key) {
            $rawValue = $request->get($key, null);
            $parsedValue = $this->dateParser->parseDate($rawValue);

            $request->attributes->set($key, $parsedValue);
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array('onKernelRequest')
        );
    }
}
