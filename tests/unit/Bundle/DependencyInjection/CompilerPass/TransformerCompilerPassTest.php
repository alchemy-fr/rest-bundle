<?php

namespace Alchemy\RestBundle\Tests\DependencyInjection\CompilerPass;

use Alchemy\RestBundle\DependencyInjection\Compiler\TransformerCompilerPass;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class TransformerCompilerPassTest extends \PHPUnit\Framework\TestCase
{
    use \Prophecy\PhpUnit\ProphecyTrait;


    const CONTAINER_CLASS = 'Symfony\Component\DependencyInjection\ContainerBuilder';

    const DEFINITION_CLASS = 'Symfony\Component\DependencyInjection\Definition';

    public function testCompilerPassIsIgnoredOnEmptyConfiguration()
    {
        $container = new ContainerBuilder();
        $pass = new TransformerCompilerPass();

        $pass->process($container);

        $this->assertFalse($container->hasDefinition('alchemy_rest.transformer_registry'));
        $this->assertCount(1, $container->getDefinitions());
    }

    public function testCompilerPassDoesNotModifyConfigurationWithoutTaggedServices()
    {
        $container = new ContainerBuilder();
        $registry = $container->register('alchemy_rest.transformer_registry');

        (new TransformerCompilerPass())->process($container);

        $this->assertSame([], $registry->getMethodCalls());
    }

    public function testCompilerPassAddsTaggedDefinitionsToTransformerService()
    {
        $container = new ContainerBuilder();
        $registry = $container->register('alchemy_rest.transformer_registry');
        $container->register('test')->addTag('alchemy_rest.transformer', ['alias' => 'transformer']);

        (new TransformerCompilerPass())->process($container);

        $calls = $registry->getMethodCalls();
        $this->assertCount(1, $calls);
        $this->assertSame('setTransformer', $calls[0][0]);
        $this->assertSame('transformer', $calls[0][1][0]);
        $this->assertEquals(new Reference('test'), $calls[0][1][1]);
    }
}
