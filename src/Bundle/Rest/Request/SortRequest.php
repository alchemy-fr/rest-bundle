<?php

namespace Alchemy\RestBundle\Request;

use Alchemy\Rest\Request\Sort;
use Alchemy\Rest\Request\SortOptions;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
     * @param OptionsResolver $resolver
     * @param array $options
     */
    public function __construct(OptionsResolver $resolver, array $options)
    {
        $options = $resolver->resolve($options);

        $this->sorts = $options['sorts'];
        $this->property = $options['sort'];
        $this->direction = $options['dir'];
    }

    /**
     * @param array $sortMap
     * @return array
     */
    public function getSorts(array $sortMap = array())
    {
        if ($this->sorts === null && $this->property !== null && $this->direction !== null) {
            $this->sorts = array(array($this->property, $this->direction));
        }

        if ($this->sorts === null) {
            return array();
        }

        if (is_string($this->sorts)) {
            $this->sorts = explode(',', $this->sorts);
        }

        $sorts = array();

        foreach ($this->sorts as $sort) {
            $sort = $this->normalizeSort($sort);
            $sort = $this->mapSortProperty($sortMap, $sort[0], $sort[1]);

            if ($sort) {
                $sorts[] = $sort;
            }
        }

        return $sorts;
    }

    /**
     * @param $sort
     * @return array
     */
    private function normalizeSort($sort)
    {
        if (is_string($sort)) {
            $sort = explode(':', $sort);
        }

        if (count($sort) < 2) {
            $sort[1] = self::SORT_ASC;

            return $sort;
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
}
