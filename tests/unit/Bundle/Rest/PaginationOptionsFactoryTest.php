<?php

namespace Alchemy\RestBundle\Tests\Bundle\Rest;

use Alchemy\RestBundle\Rest\Request\PaginationOptionsFactory;

class PaginationOptionsFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testPaginationIsResolvedProperlyWithDefaults()
    {
        $factory = new PaginationOptionsFactory('offset', 'limit');
        $pagination = $factory->create(array('offset' => 5, 'limit' => 6), array());

        $this->assertEquals(5, $pagination->getOffset(0));
        $this->assertEquals(6, $pagination->getLimit(1));
    }

    public function testPaginationIsResolvedProperlyWithOverriddenNames()
    {
        $factory = new PaginationOptionsFactory('offset', 'limit');
        $pagination = $factory->create(array('off' => 5, 'lim' => 6), array(
            'offset' => 'off',
            'limit' => 'lim'
        ));

        $this->assertEquals(5, $pagination->getOffset(0));
        $this->assertEquals(6, $pagination->getLimit(1));
    }
}
