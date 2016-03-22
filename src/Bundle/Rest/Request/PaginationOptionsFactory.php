<?php

namespace Alchemy\RestBundle\Rest\Request;

use Symfony\Component\OptionsResolver\OptionsResolver;

class PaginationOptionsFactory
{
    /**
     * @var string
     */
    private $offsetName;

    /**
     * @var string
     */
    private $limitName;

    /**
     * @param string $offsetName
     * @param string $limitName
     */
    public function __construct($offsetName, $limitName)
    {
        $this->offsetName = $offsetName;
        $this->limitName = $limitName;
    }

    /**
     * @param array $request
     * @param array $config
     * @return PaginationRequest
     */
    public function create(array $request, array $config)
    {
        $offsetName = isset($config['offset']) ? $config['offset'] : $this->offsetName;
        $limitName = isset($config['limit']) ? $config['limit'] : $this->limitName;

        $resolver = $this->configureResolver($request, $offsetName, $limitName);
        $options = $resolver->resolve($request);

        return new PaginationRequest($options[$offsetName], $options[$limitName]);
    }

    /**
     * @param array $options
     * @param string $offsetName
     * @param string $limitName
     * @return OptionsResolver
     */
    private function configureResolver(array $options, $offsetName, $limitName)
    {
        $optionsResolver = new OptionsResolver();

        if (method_exists($optionsResolver, 'setDefined')) {
            $optionsResolver->setDefined(array_merge(array(
                $offsetName,
                $limitName
            ), array_keys($options)));
        } else {
            // BC with symfony < 2.6
            $optionsResolver->setOptional(array_merge(array(
                $offsetName,
                $limitName
            ), array_keys($options)));
        }

        $optionsResolver->setDefaults(array(
            $offsetName => 0,
            $limitName => 15
        ));

        return $optionsResolver;
    }
}
