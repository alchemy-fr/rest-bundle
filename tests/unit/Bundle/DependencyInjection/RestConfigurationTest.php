<?php

namespace Alchemy\RestBundle\Tests\DependencyInjection;

use Alchemy\RestBundle\DependencyInjection\RestConfiguration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;

class RestConfigurationTest extends \PHPUnit_Framework_TestCase
{

    public function testConfigurationDefaultsAreProperlyLoaded()
    {
        $loader = new Yaml();
        $configValues = $loader->parse('alchemy_rest: []');

        $configuration = new RestConfiguration();
        $processor = new Processor();

        $config = $processor->processConfiguration($configuration, $configValues);

        $this->assertEquals($config, array(
            'dates' => array(
                'enabled' => true,
                'format' => 'Y-m-d H:i:s',
                'timezone' => 'UTC'
            ),
            'exceptions' => array(
                'enabled' => true,
                'content_types' => array('application/json'),
                'transformer' => null
            ),
            'sort' => array(
                'enabled' => true,
                'sort_parameter' => 'sort',
                'direction_parameter' => 'dir',
                'multi_sort_parameter' => 'sorts'
            ),
            'pagination' => array(
                'enabled' => true,
                'limit_parameter' => 'limit',
                'offset_parameter' => 'offset'
            )
        ));
    }
}
