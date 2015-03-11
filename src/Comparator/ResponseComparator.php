<?php
/**
 * This file is part of the biyocon.
 */

namespace Biyocon\Comparator;


use Biyocon\Http\Response;

class ResponseComparator
{
    /**
     * @var array
     * @see https://github.com/JBlond/php-diff/blob/master/lib/Diff.php $defaultOptions
     */
    private $diffOptions = [];

    /**
     * @param Response $responseA
     * @param Response $responseB
     * @return Diff
     */
    public function compare(Response $responseA, Response $responseB)
    {
        $statusDiff = $this->createDiff(
            'Http status: ' . $responseA->getStatus(),
            'Http status: ' . $responseB->getStatus()
        );
        $headerDiff = $this->createDiff(
            $responseA->getHeadersAsText(),
            $responseB->getHeadersAsText()
        );
        $bodyDiff = $this->createDiff(
            $responseA->getBody(),
            $responseB->getBody()
        );

        return new Diff($statusDiff, $headerDiff, $bodyDiff);
    }

    private function createDiff($a, $b)
    {
        return new \Diff(
            explode("\n", $a),
            explode("\n", $b),
            $this->diffOptions
        );
    }
}
