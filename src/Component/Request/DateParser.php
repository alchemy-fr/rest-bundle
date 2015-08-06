<?php

namespace Alchemy\Rest\Request;

interface DateParser 
{
    /**
     * @param $value
     * @return null|\DateTimeInterface
     */
    public function parseDate($value);
}
