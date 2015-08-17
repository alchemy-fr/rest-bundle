<?php

namespace Alchemy\RestBundle\Tests\Bundle\Rest\Request;

use Alchemy\Rest\Request\Sort;
use Alchemy\RestBundle\Rest\Request\SortRequest;

class SortRequestTest extends \PHPUnit_Framework_TestCase
{

    public function testGetSortsReturnsEmptyArrayWhenNoSortsAreSet()
    {
        $request = new SortRequest(null);

        $this->assertEmpty($request->getSorts());
    }

    public function testGetSortsReturnsCorrectValueWhenMultisortIsNull()
    {
        $request = new SortRequest(null, 'test', 'ASC');

        $this->assertContains(new Sort('test', 'ASC'), $request->getSorts(), '', false, false);
    }

    public function testGetSortsIgnoresSortPropertyWhenMultisortIsSet()
    {
        $request = new SortRequest('test:ASC', 'ignore', 'DESC');

        $this->assertNotContains(new Sort('ignore', 'DESC'), $request->getSorts(), '', false, false);
    }

    public function testGetSortsReturnsMultisorts()
    {
        $request = new SortRequest('test:ASC,hello:DESC');

        $sorts = $request->getSorts();

        $this->assertContains(new Sort('test', 'ASC'), $sorts, '', false, false);
        $this->assertContains(new Sort('hello', 'DESC'), $sorts, '', false, false);
    }

    public function testGetSortsSetsSortToAscendingWhenDirectionIsNotSpecified()
    {
        $request = new SortRequest('test,hello');

        $sorts = $request->getSorts();

        $this->assertContains(new Sort('test', 'ASC'), $sorts, '', false, false);
        $this->assertContains(new Sort('hello', 'ASC'), $sorts, '', false, false);
    }

    public function testGetSortsProperlyMapsFieldNames()
    {
        $request = new SortRequest('test,hello');
        $map = array(
            'test' => 'prod',
            'hello' => 'bye'
        );

        $sorts = $request->getSorts($map);

        $this->assertContains(new Sort('prod', 'ASC'), $sorts, '', false, false);
        $this->assertContains(new Sort('bye', 'ASC'), $sorts, '', false, false);
    }
}
