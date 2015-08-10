<?php

namespace Alchemy\RestBundle\Tests\DependencyInjection;

use Alchemy\RestBundle\DependencyInjection\RestExtension;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class RestExtensionTest extends \PHPUnit_Framework_TestCase
{

    const CONTAINER_CLASS = 'Symfony\Component\DependencyInjection\ContainerBuilder';

    const DEFINITION_CLASS = 'Symfony\Component\DependencyInjection\Definition';

    public function testListenersAreRemovedWhenNotEnabled()
    {
        $config = array('alchemy_rest' => array(
            'exceptions' => array('enabled' => false),
            'dates' => array('enabled' => false),
            'sort' => array('enabled' => false),
            'pagination' => array('enabled' => false)
        ));

        $extension = new RestExtension();
        $container = $this->prophesize(self::CONTAINER_CLASS);

        $container->addResource(Argument::any())->shouldBeCalled();
        $container->setDefinition(Argument::any(), Argument::any())->shouldBeCalled();
        $container->removeDefinition(Argument::exact('alchemy_rest.date_request_listener'))->shouldBeCalled();
        $container->removeDefinition(Argument::exact('alchemy_rest.sort_request_listener'))->shouldBeCalled();
        $container->removeDefinition(Argument::exact('alchemy_rest.paginate_request_listener'))->shouldBeCalled();
        $container->removeDefinition(Argument::exact('alchemy_rest.exception_listener'))->shouldBeCalled();

        $extension->load($config, $container->reveal());
    }

    public function testExceptionListenerArgumentsAreReplacedWhenExceptionListenerIsEnabled()
    {
        $config = array('alchemy_rest' => array(
            'exceptions' => array(
                'enabled' => true,
                'transformer' => 'transformer_reference',
                'content_types' => array('text/dummy')
            ),
            'dates' => array('enabled' => false),
            'sort' => array('enabled' => false),
            'pagination' => array('enabled' => false)
        ));

        $exceptionListener = new Definition();
        $exceptionListener->setArguments(array(null, null));

        $container = $this->prophesize(self::CONTAINER_CLASS);
        $container->addResource(Argument::any())->shouldBeCalled();
        $container->setDefinition(Argument::any(), Argument::any())->shouldBeCalled();
        $container->removeDefinition(Argument::any())->shouldBeCalled();
        $container->removeDefinition(Argument::exact('alchemy_rest.exception_listener'))->shouldNotBeCalled();
        $container->getDefinition(Argument::exact('alchemy_rest.exception_listener'))->willReturn($exceptionListener);

        $extension = new RestExtension();

        $extension->load($config, $container->reveal());

        $this->assertEquals('transformer_reference', (string) $exceptionListener->getArgument(0));
        $this->assertEquals(array('text/dummy'), $exceptionListener->getArgument(1));
    }

    public function testExceptionListenerTransformerArgumentIsNotReplacedWhenTransformerParameterIsNull()
    {
        $config = array('alchemy_rest' => array(
            'exceptions' => array(
                'enabled' => true,
                'transformer' => null,
                'content_types' => array('text/dummy')
            ),
            'dates' => array('enabled' => false),
            'sort' => array('enabled' => false),
            'pagination' => array('enabled' => false)
        ));

        $exceptionListener = new Definition();
        $exceptionListener->setArguments(array(new Reference('transformer'), null));

        $container = $this->prophesize(self::CONTAINER_CLASS);
        $container->addResource(Argument::any())->shouldBeCalled();
        $container->setDefinition(Argument::any(), Argument::any())->shouldBeCalled();
        $container->removeDefinition(Argument::any())->shouldBeCalled();
        $container->removeDefinition(Argument::exact('alchemy_rest.exception_listener'))->shouldNotBeCalled();
        $container->getDefinition(Argument::exact('alchemy_rest.exception_listener'))->willReturn($exceptionListener);

        $extension = new RestExtension();

        $extension->load($config, $container->reveal());

        $this->assertEquals('transformer', (string) $exceptionListener->getArgument(0));
        $this->assertEquals(array('text/dummy'), $exceptionListener->getArgument(1));
    }

    public function testDateParserArgumentsAreReplacedWhenDateListenerIsEnabled()
    {
        $config = array('alchemy_rest' => array(
            'exceptions' => array('enabled' => false),
            'dates' => array(
                'enabled' => true,
                'timezone' => 'date_tz_name',
                'format' => 'date_format'
            ),
            'sort' => array('enabled' => false),
            'pagination' => array('enabled' => false)
        ));

        $dateParser = new Definition();

        $container = $this->prophesize(self::CONTAINER_CLASS);
        $container->addResource(Argument::any())->shouldBeCalled();
        $container->setDefinition(Argument::any(), Argument::any())->shouldBeCalled();
        $container->removeDefinition(Argument::any())->shouldBeCalled();
        $container->removeDefinition(Argument::exact('alchemy_rest.date_request_listener'))->shouldNotBeCalled();
        $container->getDefinition(Argument::exact('alchemy_rest.date_parser'))->willReturn($dateParser);

        $extension = new RestExtension();

        $extension->load($config, $container->reveal());

        $this->assertEquals('date_tz_name', $dateParser->getArgument(0));
        $this->assertEquals('date_format', $dateParser->getArgument(1));
    }

    public function testPaginateOptionsFactoryArgumentsAreReplacedWhenPaginationListenerIsEnabled()
    {
        $config = array('alchemy_rest' => array(
            'exceptions' => array('enabled' => false),
            'dates' => array('enabled' => false),
            'sort' => array('enabled' => false),
            'pagination' => array(
                'enabled' => true,
                'limit_parameter' => 'paginate_limit_name',
                'offset_parameter' => 'paginate_offset_name'
            )
        ));

        $paginationFactory = new Definition();

        $container = $this->prophesize(self::CONTAINER_CLASS);
        $container->addResource(Argument::any())->shouldBeCalled();
        $container->setDefinition(Argument::any(), Argument::any())->shouldBeCalled();
        $container->removeDefinition(Argument::any())->shouldBeCalled();
        $container->removeDefinition(Argument::exact('alchemy_rest.paginate_request_listener'))->shouldNotBeCalled();
        $container->getDefinition(Argument::exact('alchemy_rest.paginate_options_factory'))->willReturn($paginationFactory);

        $extension = new RestExtension();

        $extension->load($config, $container->reveal());

        $this->assertEquals('paginate_offset_name', $paginationFactory->getArgument(0));
        $this->assertEquals('paginate_limit_name', $paginationFactory->getArgument(1));
    }

    public function testSortOptionsFactoryArgumentsAreReplacedWhenPaginationListenerIsEnabled()
    {
        $config = array('alchemy_rest' => array(
            'exceptions' => array('enabled' => false),
            'dates' => array('enabled' => false),
            'sort' => array(
                'enabled' => true,
                'sort_parameter' => 'sort_parameter_name',
                'direction_parameter' => 'dir_parameter_name',
                'multi_sort_parameter' => 'multi_sort_parameter_name'
            ),
            'pagination' => array('enabled' => false)
        ));

        $sortFactory = new Definition();

        $container = $this->prophesize(self::CONTAINER_CLASS);
        $container->addResource(Argument::any())->shouldBeCalled();
        $container->setDefinition(Argument::any(), Argument::any())->shouldBeCalled();
        $container->removeDefinition(Argument::any())->shouldBeCalled();
        $container->removeDefinition(Argument::exact('alchemy_rest.sort_request_listener'))->shouldNotBeCalled();
        $container->getDefinition(Argument::exact('alchemy_rest.sort_options_factory'))->willReturn($sortFactory);

        $extension = new RestExtension();

        $extension->load($config, $container->reveal());

        $this->assertEquals('sort_parameter_name', $sortFactory->getArgument(0));
        $this->assertEquals('multi_sort_parameter_name', $sortFactory->getArgument(2));
        $this->assertEquals('dir_parameter_name', $sortFactory->getArgument(1));
    }
}
