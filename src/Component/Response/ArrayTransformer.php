<?php

namespace Alchemy\Rest\Response;

use League\Fractal\Manager;
use League\Fractal\Pagination\PaginatorInterface;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class ArrayTransformer
{

    /**
     * @var Manager
     */
    private $manager;

    /**
     * @var \ArrayAccess
     */
    private $transformers;

    /**
     * @param Manager $fractalManager
     * @param \ArrayAccess $transformerRegistry
     */
    public function __construct(Manager $fractalManager, \ArrayAccess $transformerRegistry)
    {
        $this->manager = $fractalManager;
        $this->transformers = $transformerRegistry;
    }

    /**
     * @param string $key
     * @return TransformerAbstract
     */
    public function getTransformer($key)
    {
        if (! isset($this->transformers[$key])) {
            throw new \RuntimeException('Invalid transformer requested.');
        }

        return $this->transformers[$key];
    }

    /**
     * @param $key
     * @param mixed $resource
     * @param null|string|array $includes
     * @return array
     */
    public function transform($key, $resource, $includes = null)
    {
        $transformer = $this->getTransformer($key);
        $resource = (new Item($resource, $transformer));

        return $this->convertResource($resource, $includes);
    }

    /**
     * @param $key
     * @param array $resources
     * @param PaginatorInterface $paginator
     * @param null|string|array $includes
     * @return array
     */
    public function transformList($key, $resources, $includes = null, PaginatorInterface $paginator = null)
    {
        $transformer = $this->getTransformer($key);
        $resource = new Collection($resources, $transformer);

        if ($paginator !== null) {
            $resource->setPaginator($paginator);
        }

        return $this->convertResource($resource, $includes);
    }

    /**
     * @param mixed $resource
     * @param null|string|array $includes
     * @return array
     */
    private function convertResource($resource, $includes = null)
    {
        if ($includes !== null) {
            $this->manager->parseIncludes($includes);
        }

        return $this->manager->createData($resource)->toArray();
    }
}
