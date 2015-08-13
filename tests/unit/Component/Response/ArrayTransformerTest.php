<?php

namespace Alchemy\RestBundle\Tests\Response;

use Alchemy\Rest\Response\ArrayTransformer;
use Alchemy\Rest\Response\TransformerRegistry;
use League\Fractal\Manager;
use League\Fractal\Pagination\PagerfantaPaginatorAdapter;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;
use Pagerfanta\Adapter\AdapterInterface;
use Pagerfanta\Pagerfanta;

class ArrayTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \RuntimeException
     */
    public function testGetTransformerThrowsExceptionOnMissingKeys()
    {
        $transformer = new ArrayTransformer(new Manager(), new TransformerRegistry());

        $transformer->getTransformer('missing');
    }

    public function testTransformObjectReturnsCorrectArray()
    {
        $registry = new TransformerRegistry();
        $transformer = new ArrayTransformer(new Manager(), $registry);

        $registry->setTransformer('mock', new MockTransformer());

        $data = $transformer->transform('mock', new \stdClass());

        $this->assertEquals(array('data' => array('transformed' => true)), $data);
    }

    public function testTransformObjectWithIncludesReturnsCorrectArray()
    {
        $registry = new TransformerRegistry();
        $transformer = new ArrayTransformer(new Manager(), $registry);

        $registry->setTransformer('mock', new MockTransformer());

        $data = $transformer->transform('mock', new \stdClass(), array('child'));

        $this->assertEquals(array('data' => array(
            'transformed' => true,
            'child' => array(
                'data' => array('transformed' => true)
            )
        )), $data);
    }

    public function testTransformObjectCollectionReturnsCorrectArray()
    {
        $registry = new TransformerRegistry();
        $transformer = new ArrayTransformer(new Manager(), $registry);

        $registry->setTransformer('mock', new MockTransformer());

        $data = $transformer->transformList('mock', array(new \stdClass()));

        $this->assertEquals(array('data' => array(array('transformed' => true))), $data);
    }


    public function testTransformObjectCollectionWithIncludesReturnsCorrectArray()
    {
        $registry = new TransformerRegistry();
        $transformer = new ArrayTransformer(new Manager(), $registry);

        $registry->setTransformer('mock', new MockTransformer());

        $data = $transformer->transformList('mock', array(new \stdClass()), array('child'), null);

        $this->assertEquals(array('data' => array(array(
            'transformed' => true,
            'child' => array(
                'data' => array('transformed' => true)
            )
        ))), $data);
    }

    public function testTransformPaginatedObjectCollectionReturnsCorrectArray()
    {
        $collection = array(new \stdClass());
        $pager = new Pagerfanta(new MockPager($collection));
        $pagerAdapter = new PagerfantaPaginatorAdapter($pager, function () {
            return 'url';
        });

        $registry = new TransformerRegistry();
        $transformer = new ArrayTransformer(new Manager(), $registry);

        $registry->setTransformer('mock', new MockTransformer());

        $data = $transformer->transformList('mock', array(new \stdClass()), array('child'), $pagerAdapter);

        $this->assertEquals(array(
            'data' => array(array(
                'transformed' => true,
                'child' => array(
                    'data' => array('transformed' => true)
                )
            )),
            // Goal is not to test proper pagination (out of scope) but to ensure that the transformer does
            // correctly use pagination features of Fractal
            'meta' => array(
                'pagination' => array(
                    'total' => 1,
                    'count' => 1,
                    'per_page' => $pager->getMaxPerPage(),
                    'current_page' => 1,
                    'total_pages' => 1,
                    'links' => array()
                )
            )
        ), $data);
    }
}

class MockTransformer extends TransformerAbstract
{

    protected $availableIncludes = array('child');

    public function transform($object)
    {
        return array(
            'transformed' => true
        );
    }

    public function includeChild($object)
    {
        return new Item($object, $this);
    }
}

class MockPager implements AdapterInterface
{

    private $objects;

    public function __construct(array $objects)
    {
        $this->objects  = $objects;
    }

    /**
     * Returns the number of results.
     *
     * @return integer The number of results.
     */
    public function getNbResults()
    {
        return count($this->objects);
    }

    /**
     * Returns an slice of the results.
     *
     * @param integer $offset The offset.
     * @param integer $length The length.
     *
     * @return array|\Traversable The slice.
     */
    public function getSlice($offset, $length)
    {
        return array_slice($this->objects, $offset, $length);
    }

}
