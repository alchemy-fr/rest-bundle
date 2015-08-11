<?php

namespace Alchemy\Rest\Tests\Request\DateParser;

use Alchemy\Rest\Request\DateParser\FormatDateParser;

class FormatDateParserTest extends \PHPUnit_Framework_TestCase
{

    public function getInvalidDates()
    {
        return array(
            array('hfjdsfjds'),
            array(null),
            array(0),
            array(false),
            array(''),
            array(123.23)
        );
    }

    /**
     * @dataProvider getInvalidDates
     */
    public function testParseInvalidDateReturnsNull($date)
    {
        $parser = new FormatDateParser('UTC', 'Y-m-d H:i:s');

        $this->assertNull($parser->parseDate($date));
    }

    public function testParseReturnsCorrectDate()
    {
        // Using current timezone is required as this format has no TZ info
        $parser = new FormatDateParser(date_default_timezone_get(), 'Y-m-d H:i:s');
        $date = $parser->parseDate('2015-08-01 15:25:30');

        $this->assertEquals(date_default_timezone_get(), $date->getTimezone()->getName());
        $this->assertEquals('1438435530', $date->getTimestamp());
    }

    public function testParseReturnsDateWithCorrectTimezone()
    {
        $parser = new FormatDateParser('Europe/Paris', 'Y-m-d H:i:s');
        $this->assertEquals('Europe/Paris', $parser->parseDate('2015-08-01 15:25:30')->getTimezone()->getName());

        $parser = new FormatDateParser('Europe/Moscow', 'Y-m-d H:i:s');
        $this->assertEquals('Europe/Moscow', $parser->parseDate('2015-08-01 15:25:30')->getTimezone()->getName());

        $parser = new FormatDateParser('UTC', 'Y-m-d H:i:s');
        $this->assertEquals('UTC', $parser->parseDate('2015-08-01 15:25:30')->getTimezone()->getName());
    }
}
