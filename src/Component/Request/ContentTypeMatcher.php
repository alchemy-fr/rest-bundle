<?php

namespace Alchemy\Rest\Request;

use Negotiation\Negotiator;

class ContentTypeMatcher
{
    /**
     * @var Negotiator
     */
    private $negotiator;

    /**
     * @param Negotiator|null $negotiator
     */
    public function __construct(Negotiator $negotiator = null)
    {
        $this->negotiator = $negotiator ?: new Negotiator();
    }

    /**
     * @param string $acceptHeader
     * @param string[] $acceptedContentTypes
     * @return bool
     */
    public function matches($acceptHeader, array $acceptedContentTypes)
    {
        if (empty($acceptHeader)) {
            return false;
        }

        // This is in case the submitted header is not standards compliant
        if (strpos($acceptHeader, ';')) {
            list($acceptHeader, ) = explode(';', $acceptHeader);
        }

        $format = $this->negotiator->getBest($acceptHeader, $acceptedContentTypes);

        if ($format) {
            return true;
        }

        return false;
    }
}
