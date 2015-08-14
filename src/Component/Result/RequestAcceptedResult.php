<?php

namespace Alchemy\Rest\Result;

class RequestAcceptedResult
{
    private $metadata;

    public function __construct(array $metadata = array())
    {
        $this->metadata = $metadata;
    }

    public function getMetadata()
    {
        return $this->metadata;
    }
}
