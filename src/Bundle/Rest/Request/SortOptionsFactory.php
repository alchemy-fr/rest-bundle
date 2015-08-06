<?php

namespace Alchemy\RestBundle\Request;

use Alchemy\Rest\Request\SortOptions;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortOptionsFactory
{

    private $sortName;

    private $directionName;

    private $multisortName;

    public function __construct($sortName, $directionName, $multisortName)
    {
        $this->sortName = $sortName;
        $this->directionName = $directionName;
        $this->multisortName = $multisortName;
    }

    public function create(array $request, array $config)
    {
        $resolver = $this->configureResolver($request);

        return new SortRequest($resolver, $request);
    }

    /**
     * @param array $options
     * @return OptionsResolver
     */
    private function configureResolver(array $options)
    {
        $optionsResolver = new OptionsResolver();

        $optionsResolver->setDefined(array_merge(array(
            $this->sortName,
            $this->directionName,
            $this->multisortName
        ), array_keys($options)));

        $optionsResolver->setDefaults(array(
            $this->sortName => null,
            $this->directionName => null,
            $this->multisortName => null
        ));

        $optionsResolver->setAllowedValues($this->directionName, array(
            strtoupper(SortOptions::SORT_ASC),
            strtolower(SortOptions::SORT_ASC),
            strtoupper(SortOptions::SORT_DESC),
            strtolower(SortOptions::SORT_DESC),
            null
        ));

        return $optionsResolver;
    }
}
