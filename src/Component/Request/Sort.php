<?php

namespace Alchemy\Rest\Request;

class Sort
{
    /**
     * @var string
     */
    private $fieldName;

    /**
     * @var string
     */
    private $direction;

    /**
     * @param string $fieldName
     * @param string $direction
     */
    public function __construct($fieldName, $direction)
    {
        $this->fieldName = $fieldName;
        $this->direction = strtoupper($direction);
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * @return string
     */
    public function getDirection()
    {
        return $this->direction;
    }
}
