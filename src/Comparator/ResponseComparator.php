<?php
/**
 * This file is part of the biyocon.
 */

namespace Biyocon\Comparator;


use Biyocon\Http\Request;
use Biyocon\Http\Response;
use Biyocon\Mask\Mask;

class ResponseComparator
{
    /**
     * @var array
     * @see https://github.com/JBlond/php-diff/blob/master/lib/Diff.php $defaultOptions
     */
    private $diffOptions = [];

    /**
     * @var array  list of \Biyocon\Mask\Mask
     */
    private $bodyMasks = [];

    /**
     * @var array  list of \Biyocon\Mask\Mask
     */
    private $headerMasks = [];

    /**
     * @param Request $requestA
     * @param Request $requestB
     * @param Response $responseA
     * @param Response $responseB
     * @return Diff
     */
    public function compare(Request $requestA, Request $requestB, Response $responseA, Response $responseB)
    {
        $statusDiff = $this->createDiff(
            'Http status: ' . $responseA->getStatus(),
            'Http status: ' . $responseB->getStatus()
        );
        $headerDiff = $this->createDiff(
            $this->applyMasks($this->headerMasks, $responseA->getHeadersAsText(), $requestA, $responseA),
            $this->applyMasks($this->headerMasks, $responseB->getHeadersAsText(), $requestB, $responseB)
        );
        $bodyDiff = $this->createDiff(
            $this->applyMasks($this->bodyMasks, $responseA->getBody(), $requestA, $responseA),
            $this->applyMasks($this->bodyMasks, $responseB->getBody(), $requestB, $responseB)
        );

        return new Diff($statusDiff, $headerDiff, $bodyDiff);
    }

    /**
     * @param Mask $mask
     * @return ResponseComparator
     */
    public function addHeaderMask(Mask $mask)
    {
        $this->headerMasks[] = $mask;
        return $this;
    }

    /**
     * @param Mask $mask
     * @return ResponseComparator
     */
    public function addBodyMask(Mask $mask)
    {
        $this->bodyMasks[] = $mask;
        return $this;
    }

    private function createDiff($a, $b)
    {
        return new \Diff(
            explode("\n", $a),
            explode("\n", $b),
            $this->diffOptions
        );
    }

    private function applyMasks(array $masks, $text, Request $request, Response $response)
    {
        /** @var Mask $mask */
        foreach ($masks as $mask) {
            $text = $mask->mask($text, $request, $response);
        }

        return $text;
    }
}
