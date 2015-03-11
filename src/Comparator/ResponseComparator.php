<?php
/**
 * This file is part of the biyocon.
 */

namespace Biyocon\Comparator;


use Biyocon\Http\Response;

class ResponseComparator
{
    public function compare(Response $responseA, Response $responseB)
    {
        $headerDiff = $responseA->getHeadersAsText();
        $bodyDiff = $responseA->getBody();

        $diff = [
            'header' => $headerDiff,
            'body' => $bodyDiff,
        ];

        return $diff;
    }
}
