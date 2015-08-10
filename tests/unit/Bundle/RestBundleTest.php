<?php

namespace Alchemy\RestBundle\Tests;

use Alchemy\RestBundle\RestBundle;
use Prophecy\Argument;

class RestBundleTest extends \PHPUnit_Framework_TestCase
{

    const CONTAINER_CLASS = 'Symfony\Component\DependencyInjection\ContainerBuilder';

    const EXTENSION_CLASS = 'Alchemy\RestBundle\DependencyInjection\RestExtension';

    const COMPILER_PASS_CLASS = 'Alchemy\RestBundle\DependencyInjection\Compiler\TransformerCompilerPass';

    public function testGetExtension()
    {
        $bundle = new RestBundle();

        $extension = $bundle->getContainerExtension();

        $this->assertInstanceOf(self::EXTENSION_CLASS, $extension);
    }

    public function testBuildContainer()
    {
        $container = $this->prophesize(self::CONTAINER_CLASS);
        $container->addCompilerPass(Argument::type(self::COMPILER_PASS_CLASS))->shouldBeCalled();

        $bundle = new RestBundle();

        $bundle->build($container->reveal());
    }
}
