<?php

namespace Alchemy\RestBundle\Rest\Request;

use Alchemy\Rest\Request\SortOptions;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortOptionsFactory
{
    /**
     * @var string
     */
    private $sortName;

    /**
     * @var string
     */
    private $directionName;

    /**
     * @var string
     */
    private $multisortName;

    /**
     * @param string $sortName
     * @param string $directionName
     * @param string $multisortName
     */
    public function __construct($sortName, $directionName, $multisortName)
    {
        $this->sortName = $sortName;
        $this->directionName = $directionName;
        $this->multisortName = $multisortName;
    }

    /**
     * @param array $request
     * @param array $config
     * @return SortRequest
     */
    public function create(array $request, array $config)
    {
        $sortName = isset($config['sort_parameter']) ? $config['sort_parameter'] : $this->sortName;
        $directionName = isset($config['direction_parameter']) ? $config['direction_parameter'] : $this->directionName;
        $multisortName = isset($config['multi_sort_parameter']) ?
            $config['multi_sort_parameter'] : $this->multisortName;

        $resolver = $this->configureResolver($request, $sortName, $directionName, $multisortName);
        $values = $resolver->resolve($request);

        return new SortRequest($values[$multisortName], $values[$sortName], $values[$directionName]);
    }

    /**
     * @param array $options
     * @param string $sortName
     * @param string $directionName
     * @param string $multisortName
     * @return OptionsResolver
     */
    private function configureResolver(array $options, $sortName, $directionName, $multisortName)
    {
        $optionsResolver = new OptionsResolver();

        $keys = array(
            $sortName => null,
            $directionName => null,
            $multisortName => null
        );

        $optionsResolver->setDefined(array_merge(array_keys($keys), array_keys($options)));
        $optionsResolver->setDefaults($keys);

        $optionsResolver->setAllowedValues($directionName, array(
            strtoupper(SortOptions::SORT_ASC),
            strtolower(SortOptions::SORT_ASC),
            strtoupper(SortOptions::SORT_DESC),
            strtolower(SortOptions::SORT_DESC),
            null
        ));

        return $optionsResolver;
    }
}
