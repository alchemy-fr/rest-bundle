<?php

namespace Alchemy\RestBundle\Response\ExceptionTransformer;

use Alchemy\Rest\Response\ExceptionTransformer;
use Symfony\Component\HttpKernel\Exception\HttpException;

class JsonApiExceptionTransformer implements ExceptionTransformer
{

    private $debug = false;

    public function __construct($debug = false)
    {
        $this->debug = (bool) $debug;
    }

    /**
     * @param \Exception $exception
     * @return array
     */
    public function transformException(\Exception $exception)
    {
        $data = array(
            'code' => sprintf('%d', $exception->getCode()),
            'title' => $exception->getMessage()
        );

        if ($exception instanceof HttpException) {
            $data['status'] = (string) $exception->getStatusCode();
        }

        if ($this->debug) {
            $data['meta'] = array(
                'trace' => $exception->getTraceAsString(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine()
            );
        }

        return array('errors' => array($data));
    }
}
