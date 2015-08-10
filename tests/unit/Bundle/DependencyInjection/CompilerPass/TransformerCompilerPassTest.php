<?php

namespace Alchemy\RestBundle\Tests\DependencyInjection\CompilerPass;

use Alchemy\RestBundle\DependencyInjection\Compiler\TransformerCompilerPass;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class TransformerCompilerPassTest extends \PHPUnit_Framework_TestCase
{

    const CONTAINER_CLASS = 'Symfony\Component\DependencyInjection\ContainerBuilder';

    const DEFINITION_CLASS = 'Symfony\Component\DependencyInjection\Definition';

    public function testCompilerPassIsIgnoredOnEmptyConfiguration()
    {
        $container = new ContainerBuilder();
        $pass = new TransformerCompilerPass();

        $pass->process($container);

        $this->assertCount(0, $container->getDefinitions());
    }

    public function testCompilerPassDoesNotModifyConfigurationWithTaggedServices()
    {
        $container = $this->prophesize(self::CONTAINER_CLASS);
        $definition = $this->prophesize(self::DEFINITION_CLASS);

        $container->hasDefinition(Argument::any())->willReturn(true);
        $container->findDefinition(Argument::any())->willReturn($definition->reveal());
        $container->findTaggedServiceIds(Argument::any())->willReturn(array());

        $compilerPass = new TransformerCompilerPass();

        $compilerPass->process($container->reveal());
    }

    public function testCompilerPassAddsTaggedDefinitionsToTransformerService()
    {
        $container = $this->prophesize(self::CONTAINER_CLASS);
        $definition = $this->prophesize(self::DEFINITION_CLASS);
        $taggedDefinition = $this->prophesize(self::DEFINITION_CLASS);
        $taggedId = 'test';

        $definition->addMethodCall(Argument::exact('setTransformer'), Argument::that(function ($arguments) {
            if (count($arguments) != 2) {
                return false;
            }

            if ($arguments[0] !== 'transformer') {
                return false;
            }

            if (! $arguments[1] instanceof Reference && ((string)$arguments[1]) !== 'test') {
                return false;
            }

            return true;
        }))->shouldBeCalled();

        $container->hasDefinition(Argument::any())->willReturn(true);
        $container->findDefinition(Argument::any())->willReturn($definition->reveal());
        $container->findDefinition(Argument::exact($taggedId))->willReturn($taggedDefinition->reveal());

        $container->findTaggedServiceIds(Argument::any())->willReturn(array(
            'test' => array(
                array('alias' => 'transformer')
            )
        ));

        $compilerPass = new TransformerCompilerPass();

        $compilerPass->process($container->reveal());
    }
}
