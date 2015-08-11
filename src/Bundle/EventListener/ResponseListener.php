<?php

namespace Alchemy\RestBundle\EventListener;

use Alchemy\Rest\Request\PaginationOptions;
use Alchemy\Rest\Response\ArrayTransformer;
use League\Fractal\Pagination\PagerfantaPaginatorAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Router;

class ResponseListener implements EventSubscriberInterface
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var ArrayTransformer
     */
    private $transformer;

    /**
     * @param ArrayTransformer $transformer
     * @param Router $router
     */
    public function __construct(ArrayTransformer $transformer, Router $router)
    {
        $this->transformer = $transformer;
        $this->router = $router;
    }

    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();

        if (!$request->attributes->has('_fractal')) {
            return;
        }

        $config = $this->normalizeConfig($request->attributes->get('_fractal', array(), true));
        $includes = $request->query->get('include', null);
        $data = $event->getControllerResult();

        if ($config['list']) {
            $transformedData = $this->transformer->transformList(
                $config['name'],
                $data,
                $includes,
                $this->buildPaginatorAdapter($data, $request)
            );
        } else {
            $transformedData = $this->transformer->transform(
                $config['name'],
                $data,
                $includes
            );
        }

        $event->setResponse(new JsonResponse($transformedData));
    }

    protected function normalizeConfig(array $config)
    {
        if (!isset($config['name']) || trim($config['name'] == '')) {
            throw new \RuntimeException('Transformer key is not set.');
        }

        $config['list'] = (bool)(isset($config['list']) ? $config['list'] : false);

        return $config;
    }

    /**
     * @param $data
     * @param $request
     * @return null|PagerfantaPaginatorAdapter
     */
    protected function buildPaginatorAdapter($data, $request)
    {
        if (!$data instanceof Pagerfanta) {
            return null;
        }

        $pagination = $request->attributes->get('_pagination');

        if (!$pagination instanceof PaginationOptions) {
            return null;
        }

        return new PagerfantaPaginatorAdapter($data, function ($page) use ($request, $pagination) {
            $limit = $pagination->getLimit();
            $params = array_merge($request->query->all(), $request->attributes->get('_route_params', array()), array(
                'limit' => $limit,
                'offset' => max(max($page - 1, 0) * max($limit, 0) - 1, 0)
            ));

            return $this->router->generate($request->attributes->get('_route'), $params);
        });
    }

    public static function getSubscribedEvents()
    {
        return array(KernelEvents::VIEW => 'onKernelView');
    }
}
