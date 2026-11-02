<?php

namespace Alchemy\Rest\Tests\Result;

use Alchemy\Rest\Result\SuccessResult;

class SuccessResultTest extends \PHPUnit\Framework\TestCase
{

    public function testGetMetadataReturnsOriginalMetadata()
    {
        $result = new SuccessResult(array('test' => true));

        $this->assertEquals(array('test' => true), $result->getMetadata());
    }
}
