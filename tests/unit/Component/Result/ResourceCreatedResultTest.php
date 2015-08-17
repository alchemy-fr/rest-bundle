<?php

namespace Alchemy\Rest\Tests\Result;

use Alchemy\Rest\Result\ResourceCreatedResult;

class ResourceCreatedResultTest extends \PHPUnit_Framework_TestCase
{

    public function testGetResourceReturnsCorrectResource()
    {
        $resource = new \stdClass();
        $resource->test = true;

        $result = new ResourceCreatedResult($resource);

        $this->assertEquals($resource, $result->getResource());
    }

    public function testGetMetadataReturnsCorrectMetadata()
    {
        $resource = new \stdClass();
        $result = new ResourceCreatedResult($resource, array('meta' => true));

        $this->assertEquals(array('meta' => true), $result->getMetadata());
    }
}
