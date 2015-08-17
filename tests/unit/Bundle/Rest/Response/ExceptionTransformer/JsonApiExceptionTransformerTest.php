<?php

namespace Alchemy\RestBundle\Tests\Response\ExceptionTransformer;

use Alchemy\RestBundle\Rest\Response\ExceptionTransformer\JsonApiExceptionTransformer;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class JsonApiExceptionTransformerTest extends \PHPUnit_Framework_TestCase
{

    public function testTransformedExceptionDataMatchesJsonApiSpecificationStructure()
    {
        $transformer = new JsonApiExceptionTransformer(false);
        $exception = new \Exception('error message', 10, null);

        $data = $transformer->transformException($exception);

        $this->assertArrayHasKey('errors', $data);
        $this->assertCount(1, $data['errors']);

        $this->assertArraySubset(array(
            'code' => 10,
            'title' => 'error message'
        ), $data['errors'][0]);
    }

    public function testTransformedHttpExceptionDataHasStatusKey()
    {
        $transformer = new JsonApiExceptionTransformer(false);
        $exception = new BadRequestHttpException('error message');

        $data = $transformer->transformException($exception);

        $this->assertArraySubset(array(
            'status' => 400
        ), $data['errors'][0]);
    }
}
