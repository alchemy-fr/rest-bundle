<?php

namespace Alchemy\RestBundle\Tests\Bundle\Rest\Request;

use Alchemy\Rest\Request\Sort;
use Alchemy\RestBundle\Rest\Request\SortOptionsFactory;

class SortOptionsFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testSortIsResolvedProperlyWithDefaults()
    {
        $factory = new SortOptionsFactory('sort', 'dir', 'sorts');
        $sorts = $factory->create(array(
            'sort' => 'test',
            'dir' => 'asc'
        ), array())->getSorts();

        $this->assertCount(1, $sorts);
        $this->assertEquals(new Sort('test', 'ASC'), $sorts[0]);
    }

    public function testSortIsResolvedProperlyWithOverridenDefaults()
    {
        $factory = new SortOptionsFactory('sort', 'dir', 'sorts');
        $sorts = $factory->create(array(
            'prop' => 'test',
            'order' => 'asc'
        ), array('sort_parameter' => 'prop', 'direction_parameter' => 'order'))->getSorts();

        $this->assertCount(1, $sorts);
        $this->assertEquals(new Sort('test', 'ASC'), $sorts[0]);
    }


    public function testMultiSortIsResolvedProperlyWithDefaults()
    {
        $factory = new SortOptionsFactory('sort', 'dir', 'sorts');
        $sorts = $factory->create(array(
            'sorts' => 'test:asc'
        ), array())->getSorts();

        $this->assertCount(1, $sorts);
        $this->assertEquals(new Sort('test', 'ASC'), $sorts[0]);
    }

    public function testMultiSortIsResolvedProperlyWithOverridenDefaults()
    {
        $factory = new SortOptionsFactory('sort', 'dir', 'sorts');
        $sorts = $factory->create(array(
            'props' => 'test:asc'
        ), array('multi_sort_parameter' => 'props'))->getSorts();

        $this->assertCount(1, $sorts);
        $this->assertEquals(new Sort('test', 'ASC'), $sorts[0]);
    }
}
