<?php

namespace Alchemy\Rest\Request\DateParser;

use Alchemy\Rest\Request\DateParser;

class UtcDateParser implements DateParser
{

    const FORMAT = 'Y-m-d\TH:i:s\Z';

    private $format;

    private $timezone;

    public function __construct($timezoneName = 'UTC', $format = null)
    {
        $this->timezone = new \DateTimeZone($timezoneName);
        $this->format = $format ?: self::FORMAT;
    }

    /**
     * @param $value
     * @return null|\DateTimeInterface
     */
    public function parseDate($value)
    {
        $date = \DateTimeImmutable::createFromFormat($this->format, $value, $this->timezone);

        if ($date === false) {
            return null;
        }

        return $date;
    }
}
