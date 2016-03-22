<?php

namespace Alchemy\RestBundle\Rest\Request;

use Alchemy\Rest\Request\Sort;
use Alchemy\Rest\Request\SortOptions;

class SortRequest implements SortOptions
{
    /**
     * @var string|array
     */
    private $sorts;

    /**
     * @var string
     */
    private $property;

    /**
     * @var string
     */
    private $direction;

    /**
     * @param $sorts
     * @param string|null $property
     * @param string|null $direction
     */
    public function __construct($sorts, $property = null, $direction = null)
    {
        $this->sorts = $sorts;
        $this->property = $property;
        $this->direction = $direction;
    }

    /**
     * @param array $sortMap
     * @return array
     */
    public function getSorts(array $sortMap = array())
    {
        $sorts = array();

        foreach ($this->normalizeSorts($this->sorts) as $sort) {
            $sort = $this->extractSortProperty($sort);
            $sort = $this->mapSortProperty($sortMap, $sort[0], $sort[1]);

            if ($sort) {
                $sorts[] = $sort;
            }
        }

        return $sorts;
    }

    /**
     * @return array|string|null
     */
    private function normalizeSorts($sorts)
    {
        $sorts = $this->coerceToDefaultValue($sorts);

        if (is_string($sorts)) {
            $sorts = explode(',', $sorts);
        }

        if ($sorts === null) {
            $sorts = array();
        }

        return $sorts;
    }

    /**
     * @param $sort
     * @return array
     */
    private function extractSortProperty($sort)
    {
        if (is_string($sort)) {
            $sort = explode(':', $sort);
        }

        if (count($sort) < 2) {
            $sort[1] = self::SORT_ASC;
        }

        return $sort;
    }

    /**
     * @param array $sortMap
     * @param $sort
     * @param $direction
     * @return Sort
     */
    private function mapSortProperty(array $sortMap, $sort, $direction)
    {
        if (isset($sortMap[$sort])) {
            $sort = $sortMap[$sort];
        }

        return new Sort($sort, $direction);
    }

    /**
     * @param $sorts
     * @return array
     */
    private function coerceToDefaultValue($sorts)
    {
        if ($sorts === null && $this->property !== null && $this->direction !== null) {
            $sorts = array(array($this->property, $this->direction));
        }

        return $sorts;
    }
}
