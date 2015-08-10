<?php

namespace Alchemy\Rest\Tests\Response\ExceptionTransformer;

use Alchemy\Rest\Response\ExceptionTransformer\DefaultExceptionTransformer;

class DefaultExceptionTransformerTest extends \PHPUnit_Framework_TestCase
{

    private function throwDummyException($message, $code)
    {
        throw new \RuntimeException($message, $code);
    }

    public function testTransformedExceptionArrayContainsCorrectKeys()
    {
        $exceptionMessage = 'dummy';
        $exceptionCode = 10;
        $transformedException = null;

        $transformer = new DefaultExceptionTransformer(false);

        try {
            $this->throwDummyException($exceptionMessage, $exceptionCode);
        }
        catch (\Exception $exception) {
            $transformedException = $transformer->transformException($exception);
        }

        $this->assertNotNull($transformedException);
        $this->assertArrayHasKey('message', $transformedException);
        $this->assertEquals($exceptionMessage, $transformedException['message']);
        $this->assertArrayHasKey('code', $transformedException);
        $this->assertEquals($exceptionCode, $transformedException['code']);
    }


    public function testTransformedExceptionArrayContainsCorrectKeysInDebugMode()
    {
        $exceptionMessage = 'dummy';
        $exceptionCode = 10;
        $transformedException = null;

        $transformer = new DefaultExceptionTransformer(true);

        try {
            $this->throwDummyException($exceptionMessage, $exceptionCode);
        }
        catch (\Exception $exception) {
            $transformedException = $transformer->transformException($exception);
        }

        $this->assertNotNull($transformedException);
        $this->assertArrayHasKey('message', $transformedException);
        $this->assertEquals($exceptionMessage, $transformedException['message']);
        $this->assertArrayHasKey('code', $transformedException);
        $this->assertEquals($exceptionCode, $transformedException['code']);

        $this->assertArrayHasKey('trace', $transformedException);
        $this->assertArrayHasKey('exception', $transformedException);
        $this->assertEquals('RuntimeException', $transformedException['exception']);
    }
}
