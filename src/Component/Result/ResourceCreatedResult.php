<?php

namespace Alchemy\Rest\Result;

class ResourceCreatedResult
{
    /**
     * @var mixed
     */
    private $resource;

    /**
     * @var array
     */
    private $metadata;

    /**
     * @param mixed $resource
     * @param array $metadata
     */
    public function __construct($resource, array $metadata = array())
    {
        $this->resource = $resource;
        $this->metadata = $metadata;
    }

    /**
     * @return mixed
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return array
     */
    public function getMetadata()
    {
        return $this->metadata;
    }
}
