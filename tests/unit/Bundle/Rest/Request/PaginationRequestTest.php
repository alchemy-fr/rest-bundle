<?php

namespace Alchemy\RestBundle\Tests\Bundle\Rest\Request;

use Alchemy\RestBundle\Rest\Request\PaginationRequest;

class PaginationRequestTest extends \PHPUnit_Framework_TestCase
{

    public function testGetLimitReturnsCorrectValue()
    {
        $request = new PaginationRequest(0, 10);

        $this->assertEquals(10, $request->getLimit());
    }

    public function testGetLimitReturnsDefaultValueWhenLimitIsNotStrictlyPositive()
    {
        $request = new PaginationRequest(0, 0);

        $this->assertEquals(10, $request->getLimit(10));

        $request = new PaginationRequest(0, -1);

        $this->assertEquals(10, $request->getLimit(10));
    }

    public function testGetLimitDefaultValueIsIgnoredWhenDefaultIsNull()
    {
        $request = new PaginationRequest(0, -1);

        $this->assertEquals(-1, $request->getLimit());
    }

    public function testGetOffset()
    {
        $request = new PaginationRequest(10, 0);

        $this->assertEquals(10, $request->getOffset());
    }

    public function testGetOffsetReturnsDefaultValueWhenOffsetIsNotStrictlyPositive()
    {
        $request = new PaginationRequest(0, 0);

        $this->assertEquals(10, $request->getOffset(10));

        $request = new PaginationRequest(-1, 0);

        $this->assertEquals(10, $request->getOffset(10));
    }

    public function testGetOffsetDefaultValueIsIgnoredWhenDefaultIsNull()
    {
        $request = new PaginationRequest(-1, 0);

        $this->assertEquals(-1, $request->getOffset());
    }

    /**
     * @dataProvider providesPageComputation
     */
    public function testItProperlyComputeCurrentPage($offset, $limit, $page)
    {
        $request = new PaginationRequest($offset, $limit);

        $this->assertEquals($page, $request->getCurrentPage($limit));
    }

    public function providesPageComputation()
    {
        return [
            [0, 10, 1],
            [9, 10, 1],
            [10, 10, 2],
            [15, 10, 2],
            [29, 15, 2],
            [30, 15, 3],
        ];
    }
}
