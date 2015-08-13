<?php

namespace Alchemy\Rest\Response;

use League\Fractal\TransformerAbstract;

class TransformerRegistry implements \ArrayAccess
{

    private $transformers = array();

    /**
     * @param string $key
     * @param TransformerAbstract $transformer
     */
    public function setTransformer($key, TransformerAbstract $transformer)
    {
        $this->transformers[$key] = $transformer;
    }

    public function offsetExists($offset)
    {
        return isset($this->transformers[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->transformers[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if (! $value instanceof TransformerAbstract) {
            throw new \InvalidArgumentException('Value must be an instance of League\Fractal\AbstractTransformer');
        }

        $this->transformers[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->transformers[$offset]);
    }
}
