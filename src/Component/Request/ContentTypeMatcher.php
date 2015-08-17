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
        $format = $this->negotiator->getBest($acceptHeader, $acceptedContentTypes);

        if ($format) {
            return true;
        }

        return false;
    }
}
