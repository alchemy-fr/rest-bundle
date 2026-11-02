<?php

namespace Alchemy\Rest\Tests\Result;

use Alchemy\Rest\Result\BadRequestResult;

class BadRequestResultTest extends \PHPUnit\Framework\TestCase
{

    public function testGetMetadataReturnsOriginalMeta()
    {
        $result = new BadRequestResult(array('test' => true));

        $this->assertEquals(array('test' => true), $result->getMetadata());
    }
}
