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

        $this->assertArrayHasKey('error', $transformedException);

        $this->assertArrayHasKey('message', $transformedException['error']);
        $this->assertEquals($exceptionMessage, $transformedException['error']['message']);
        $this->assertArrayHasKey('code', $transformedException['error']);
        $this->assertEquals($exceptionCode, $transformedException['error']['code']);
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

        $this->assertArrayHasKey('error', $transformedException);

        $this->assertArrayHasKey('message', $transformedException['error']);
        $this->assertEquals($exceptionMessage, $transformedException['error']['message']);
        $this->assertArrayHasKey('code', $transformedException['error']);
        $this->assertEquals($exceptionCode, $transformedException['error']['code']);

        $this->assertArrayHasKey('trace', $transformedException['error']);
        $this->assertArrayHasKey('exception', $transformedException['error']);
        $this->assertEquals('RuntimeException', $transformedException['error']['exception']);
    }
}
