<?php

namespace Alchemy\RestProvider\Middleware;

use Symfony\Component\HttpFoundation\Request;

class SetEncodingAttributeMiddleFactory
{

    public function __invoke($decodeRequest = true, $encodeResponse = true)
    {
        return function (Request $request) use ($decodeRequest, $encodeResponse) {
            $restAttribute = $request->attributes->get('_rest', array());

            $restAttribute['decode_request'] = (bool) $decodeRequest;
            $restAttribute['encode_response'] = (bool) $encodeResponse;

            $request->attributes->set('_rest', $restAttribute);
        };
    }
}
