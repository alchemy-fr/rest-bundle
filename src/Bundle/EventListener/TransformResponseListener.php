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

class TransformResponseListener implements EventSubscriberInterface
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

        if (!$request->attributes->has('_rest')) {
            return;
        }

        $config = $this->normalizeConfig($request->attributes->get('_rest', array(), true));
        $includes = $request->query->get('include', null);
        $data = $event->getControllerResult();

        $transformedData = $this->transformResult($config, $data, $includes, $request);

        $event->setControllerResult($transformedData);
    }

    protected function normalizeConfig(array $config)
    {
        if (!isset($config['transform']) || trim($config['transform'] == '')) {
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

    /**
     * @param $config
     * @param $data
     * @param $includes
     * @param $request
     * @return array
     */
    private function transformResult($config, $data, $includes, $request)
    {
        if ($config['list']) {
            return  $this->transformer->transformList(
                $config['transform'],
                $data,
                $includes,
                $this->buildPaginatorAdapter($data, $request)
            );
        }

        return $transformedData = $this->transformer->transform(
            $config['transform'],
            $data,
            $includes
        );
    }

    public static function getSubscribedEvents()
    {
        return array(KernelEvents::VIEW => 'onKernelView');
    }
}
