<?php

namespace Alchemy\Rest\Request;

interface PaginationOptions
{
    /**
     * @return int
     */
    public function getLimit($defaultValue = null);

    /**
     * @return int
     */
    public function getOffset($defaultValue = null);

    /**
     * @param int $defaultPageSize
     * @return int
     */
    public function getCurrentPage($defaultPageSize = 10);
}
