<?php

namespace Alchemy\RestBundle\Tests\Response;

use Alchemy\Rest\Response\TransformerRegistry;
use League\Fractal\TransformerAbstract;

class TransformerRegistryTest extends \PHPUnit_Framework_TestCase
{

    public function testKeysAreCorrectlySetBySetTransformerCalls()
    {
        $registry = new TransformerRegistry();
        $mock = new MockRegistryTransformer();

        $registry->setTransformer('mock', $mock);

        $this->assertTrue($registry->offsetExists('mock'));
        $this->assertSame($mock, $registry->offsetGet('mock'));
    }

    public function testKeysAreCorrectlySetByOffsetSetCalls()
    {
        $registry = new TransformerRegistry();
        $mock = new MockRegistryTransformer();

        $registry->offsetSet('mock', $mock);

        $this->assertTrue($registry->offsetExists('mock'));
        $this->assertSame($mock, $registry->offsetGet('mock'));
    }

    public function testOffsetExistsReturnsFalseWhenKeyWasNotSet()
    {
        $registry = new TransformerRegistry();

        $this->assertFalse($registry->offsetExists('mock'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testOffsetSetThrowsExceptionWhenUsedWithInvalidType()
    {
        $registry = new TransformerRegistry();

        $registry->offsetSet('mock', new \stdClass());
    }

    public function testOffsetUnsetCorrectlyUnsetsValue()
    {
        $registry = new TransformerRegistry();

        $registry->offsetSet('mock', new MockRegistryTransformer());
        $registry->offsetUnset('mock');

        $this->assertFalse($registry->offsetExists('mock'));
    }
}

class MockRegistryTransformer extends TransformerAbstract
{

}
