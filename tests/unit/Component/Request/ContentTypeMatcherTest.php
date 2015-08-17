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

    }

    public function testMatchingContentTypeNotInWhitelistRejectsIt()
    {
        $matcher = new ContentTypeMatcher();

        $this->assertFalse($matcher->matches('text/plain', array('application/json')));
    }
}
