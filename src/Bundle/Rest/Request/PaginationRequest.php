<?php

namespace Alchemy\RestBundle\Rest\Request;

use Alchemy\Rest\Request\PaginationOptions;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaginationRequest implements PaginationOptions
{

    /**
     * @var int
     */
    private $offset;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var OptionsResolver
     */
    private $optionsResolver;

    public function __construct(array $options, $offsetName, $limitName)
    {
        $this->configureResolver($options);

        $options = $this->optionsResolver->resolve($options);

        $this->offset = (int) $options[$offsetName];
        $this->limit = (int) $options[$limitName];
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param array $options
     */
    private function configureResolver(array $options)
    {
        $this->optionsResolver = new OptionsResolver();

        $this->optionsResolver->setDefined(array_merge(array(
            'offset',
            'limit'
        ), array_keys($options)));

        $this->optionsResolver->setDefaults(array(
            'offset' => 0,
            'limit' => 15
        ));
    }
}
