<?php

namespace Alchemy\Rest\Tests\Request;

use Alchemy\Rest\Request\ContentTypeMatcher;

class ContentTypeMatcherTest extends \PHPUnit_Framework_TestCase
{

    public function getCommonContentTypes()
    {
        return array(
            array('text/plain'),
            array('text/html'),
            array('text/xml'),
            array('application/json'),
            array('application/json;charset=UTF-8'),
            array('application/vnd.api+json'),
        );
    }

    /**
     * @dataProvider getCommonContentTypes()
     */
    public function testMatchingCatchAllContentTypeAcceptsAllTypes($contentType)
    {
        $matcher = new ContentTypeMatcher();

        $this->assertTrue($matcher->matches(
            '*/*',
            array($contentType)),
            'Catch all header should match all types'
        );
    }

    /**
     * @dataProvider getCommonContentTypes()
     */
    public function testMatchExactContentTypesAcceptsIt($contentType)
    {
        $matcher = new ContentTypeMatcher();

        $this->assertTrue($matcher->matches($contentType, array($contentType)));
    }

    public function testMatchingContentTypeNotInWhitelistRejectsIt()
    {
        $matcher = new ContentTypeMatcher();

        $this->assertFalse($matcher->matches('text/plain', array('application/json')));
    }

    public function testFirefoxContentTypeIssue()
    {
        $matcher = new ContentTypeMatcher();

        $this->assertTrue($matcher->matches('application/json; charset=UTF-8', array('application/json')));
    }

    public function testMatchEmptyContentTypeReturnsFalse()
    {
        $matcher = new ContentTypeMatcher();

        $this->assertFalse($matcher->matches('', array('text/html')));
    }
}
