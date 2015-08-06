<?php

namespace Alchemy\Rest\Response\ExceptionTransformer;

use Alchemy\Rest\Response\ExceptionTransformer;

class DefaultExceptionTransformer implements ExceptionTransformer
{
    /**
     * @var bool
     */
    private $debug;

    /**
     * @param bool $debug
     */
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
            'code' => $exception->getCode(),
            'message' => $exception->getMessage()
        );

        if ($this->debug === true) {
            $data['exception'] = get_class($exception);
            $data['trace'] = $exception->getTraceAsString();
        }

        return $data;
    }
}
