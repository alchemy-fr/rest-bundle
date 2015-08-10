<?php

namespace Alchemy\RestBundle\Tests\Response;

use Alchemy\Rest\Response\ArrayTransformer;
use League\Fractal\Manager;
use League\Fractal\TransformerAbstract;

class ArrayTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \RuntimeException
     */
    public function testGetTransformerThrowsExceptionOnMissingKeys()
    {
        $transformer = new ArrayTransformer(new Manager());

        $transformer->getTransformer('missing');
    }

    public function testTransformObjectReturnsCorrectArray()
    {
        $transformer = new ArrayTransformer(new Manager());
        $transformer->setTransformer('mock', new MockTransformer());

        $data = $transformer->transform('mock', new \stdClass());

        $this->assertEquals(array('data' => array('transformed' => true)), $data);
    }

    public function testTransformObjectCollectionReturnsCorrectArray()
    {
        $transformer = new ArrayTransformer(new Manager());
        $transformer->setTransformer('mock', new MockTransformer());

        $data = $transformer->transformList('mock', array(new \stdClass()));

        $this->assertEquals(array('data' => array(array('transformed' => true))), $data);
    }
}

class MockTransformer extends TransformerAbstract
{
    public function transform($object)
    {
        return array(
            'transformed' => true
        );
    }
}
