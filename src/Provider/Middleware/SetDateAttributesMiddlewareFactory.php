<?php

namespace Alchemy\RestProvider\Middleware;

use Symfony\Component\HttpFoundation\Request;

class SetDateAttributesMiddlewareFactory
{
    public function __invoke(array $dates = array())
    {
        return function (Request $request) use ($dates) {
            if (! empty($dates)) {
                $request->attributes->set('_dates', $dates);
            }
        };
    }
}
