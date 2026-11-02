<?php

namespace Alchemy\RestBundle\Tests\Response\ExceptionTransformer;

use Alchemy\RestBundle\Rest\Response\ExceptionTransformer\JsonApiExceptionTransformer;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class JsonApiExceptionTransformerTest extends \PHPUnit\Framework\TestCase
{

    public function testTransformedExceptionDataMatchesJsonApiSpecificationStructure()
    {
        $transformer = new JsonApiExceptionTransformer(false);
        $exception = new \Exception('error message', 10, null);

        $data = $transformer->transformException($exception);

        $this->assertArrayHasKey('errors', $data);
        $this->assertCount(1, $data['errors']);

        $actual = $data['errors'][0];
        $this->assertEquals(array_replace_recursive($actual, array(
            'code' => 10,
            'title' => 'error message'
        )), $actual);
    }

    public function testTransformedHttpExceptionDataHasStatusKey()
    {
        $transformer = new JsonApiExceptionTransformer(false);
        $exception = new BadRequestHttpException('error message');

        $data = $transformer->transformException($exception);

        $actual = $data['errors'][0];
        $this->assertEquals(array_replace_recursive($actual, array(
            'status' => 400
        )), $actual);
    }
}
