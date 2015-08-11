<?php

namespace Alchemy\Rest\Response;

interface ExceptionTransformer
{
    /**
     * @param \Exception $exception
     * @return array
     */
    public function transformException(\Exception $exception);
}
