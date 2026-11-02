<?php

namespace Alchemy\RestBundle\Tests\DependencyInjection;

use Alchemy\RestBundle\DependencyInjection\RestExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RestExtensionTest extends \PHPUnit\Framework\TestCase
{
    private function load(array $config): ContainerBuilder
    {
        $container = new ContainerBuilder();

        (new RestExtension())->load(['alchemy_rest' => $config + [
            'exceptions' => ['enabled' => false],
            'dates' => ['enabled' => false],
            'sort' => ['enabled' => false],
            'pagination' => ['enabled' => false],
        ]], $container);

        return $container;
    }

    public function testListenersAreRemovedWhenNotEnabled()
    {
        $container = $this->load([]);

        foreach (['date_request_listener', 'sort_request_listener', 'paginate_request_listener', 'exception_listener'] as $listener) {
            $this->assertFalse($container->hasDefinition('alchemy_rest.'.$listener), $listener);
        }
    }

    public function testExceptionListenerArgumentsAreReplacedWhenExceptionListenerIsEnabled()
    {
        $listener = $this->load(['exceptions' => [
            'enabled' => true,
            'transformer' => 'transformer_reference',
            'content_types' => ['text/dummy'],
        ]])->getDefinition('alchemy_rest.exception_listener');

        $this->assertEquals(new Reference('transformer_reference'), $listener->getArgument(0));
        $this->assertSame(['text/dummy'], $listener->getArgument(2));
    }

    public function testExceptionListenerTransformerArgumentIsNotReplacedWhenTransformerParameterIsNull()
    {
        $listener = $this->load(['exceptions' => [
            'enabled' => true,
            'transformer' => null,
            'content_types' => ['text/dummy'],
        ]])->getDefinition('alchemy_rest.exception_listener');

        $this->assertEquals(new Reference('alchemy_rest.content_type_matcher'), $listener->getArgument(0));
        $this->assertSame(['text/dummy'], $listener->getArgument(2));
    }

    public function testDateParserArgumentsAreReplacedWhenDateListenerIsEnabled()
    {
        $parser = $this->load(['dates' => [
            'enabled' => true,
            'timezone' => 'date_tz_name',
            'format' => 'date_format',
        ]])->getDefinition('alchemy_rest.date_parser');

        $this->assertSame('date_tz_name', $parser->getArgument(0));
        $this->assertSame('date_format', $parser->getArgument(1));
    }

    public function testPaginateOptionsFactoryArgumentsAreReplacedWhenPaginationListenerIsEnabled()
    {
        $factory = $this->load(['pagination' => [
            'enabled' => true,
            'offset_parameter' => 'offset_name',
            'limit_parameter' => 'limit_name',
        ]])->getDefinition('alchemy_rest.paginate_options_factory');

        $this->assertSame('offset_name', $factory->getArgument(0));
        $this->assertSame('limit_name', $factory->getArgument(1));
    }

    public function testSortOptionsFactoryArgumentsAreReplacedWhenSortListenerIsEnabled()
    {
        $factory = $this->load(['sort' => [
            'enabled' => true,
            'sort_parameter' => 'sort_name',
            'direction_parameter' => 'direction_name',
            'multi_sort_parameter' => 'multi_name',
        ]])->getDefinition('alchemy_rest.sort_options_factory');

        $this->assertSame(['sort_name', 'direction_name', 'multi_name'], $factory->getArguments());
    }
}
