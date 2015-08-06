<?php

namespace Alchemy\RestBundle\Request;

use Alchemy\RestBundle\Rest\Request\PaginationRequest;

class PaginationOptionsFactory
{
    /**
     * @var string
     */
    private $offsetName;

    /**
     * @var string
     */
    private $limitName;

    /**
     * @param string $offsetName
     * @param string $limitName
     */
    public function __construct($offsetName, $limitName)
    {
        $this->offsetName = $offsetName;
        $this->limitName = $limitName;
    }

    /**
     * @param array $request
     * @param array $config
     * @return PaginationRequest
     */
    public function create(array $request, array $config)
    {
        $offsetName = isset($config['offset']) ? $config['offset'] : $this->offsetName;
        $limitName = isset($config['limit']) ? $config['limit'] : $this->limitName;

        return new PaginationRequest($request, $offsetName, $limitName);
    }
}
