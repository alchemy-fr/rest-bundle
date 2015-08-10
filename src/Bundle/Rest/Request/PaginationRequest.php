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
     * @param null|int $defaultValue
     * @return int
     */
    public function getOffset($defaultValue = null)
    {
        return $this->offset > 0 || $defaultValue === null ? $this->offset : (int) $defaultValue;
    }

    /**
     * @param null|int $defaultValue
     * @return int
     */
    public function getLimit($defaultValue = null)
    {
        return $this->limit > 0 || $defaultValue === null ? $this->limit : (int) $defaultValue;
    }
}
