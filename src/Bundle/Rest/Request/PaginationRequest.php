<?php

namespace Alchemy\RestBundle\Rest\Request;

use Alchemy\Rest\Request\PaginationOptions;

/**
 * Class PaginationRequest
 * @package Alchemy\RestBundle\Rest\Request
 */
class PaginationRequest implements PaginationOptions
{

    /**
     * @var int
     */
    private $offset;

    /**
     * @var int
     */
    private $limit;

    /**
     * @param int $offset
     * @param int $limit
     */
    public function __construct($offset, $limit)
    {
        $this->offset = (int) $offset;
        $this->limit = (int) $limit;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }
}
