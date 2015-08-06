<?php

namespace Alchemy\Rest\Request;

interface PaginationOptions 
{
    /**
     * @return int
     */
    public function getLimit();

    /**
     * @return int
     */
    public function getOffset();
}
