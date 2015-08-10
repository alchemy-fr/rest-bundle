<?php

namespace Alchemy\Rest\Tests\Request;

use Alchemy\Rest\Request\Sort;

class SortTest extends \PHPUnit_Framework_TestCase
{

    public function testGetFieldNameReturnsAssignedValue()
    {
        $sort = new Sort('test', 'ASC');

        $this->assertEquals('test', $sort->getFieldName());
    }

    public function testGetGetDirectionReturnsAssignedValue()
    {
        $sort = new Sort('test', 'ASC');

        $this->assertEquals('ASC', $sort->getDirection());
    }

    public function testGetGetDirectionAlwaysReturnsUpperCasedValue()
    {
        $sort = new Sort('test', 'asc');

        $this->assertEquals('ASC', $sort->getDirection());

        $sort = new Sort('test', 'desc');

        $this->assertEquals('DESC', $sort->getDirection());
    }
}
