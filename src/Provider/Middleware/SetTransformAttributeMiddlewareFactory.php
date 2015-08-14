<?php

namespace Alchemy\RestProvider\Middleware;

use Symfony\Component\HttpFoundation\Request;

class SetTransformAttributeMiddlewareFactory
{
    public function __invoke($transformerKey, $isList)
    {
        return function (Request $request) use ($transformerKey, $isList) {
            $restAttribute = $request->attributes->get('_rest', array());

            $restAttribute['transform'] = $transformerKey;
            $restAttribute['list'] = (bool) $isList;

            $request->attributes->set('_rest', $restAttribute);
        };
    }
}
