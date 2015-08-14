<?php

namespace Alchemy\RestProvider\Middleware;

use Symfony\Component\HttpFoundation\Request;

class SetPaginationAndSortAttributesMiddlewareFactory
{

    public function __invoke($parseSorts = true, $parsePagination = true)
    {
        return function (Request $request) use ($parseSorts, $parsePagination) {
            $request->attributes->set('_paginate', (bool) $parsePagination);
            $request->attributes->set('_sort', (bool) $parseSorts);
        };
    }
}
