<?php

namespace Alchemy\Rest\Tests\Result;

use Alchemy\Rest\Result\RequestAcceptedResult;

class RequestAcceptedResultTest extends \PHPUnit_Framework_TestCase
{

    public function testGetMetadataReturnsOriginalMeta()
    {
        $result = new RequestAcceptedResult(array(
            'test' => true
        ));

        $this->assertEquals(array('test' => true), $result->getMetadata());
    }
}
