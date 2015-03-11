<?php
/**
 * This file is part of the biyocon.
 */

namespace Biyocon\Comparator;


use Biyocon\Http\Request;
use Biyocon\Http\Response;

class Result
{
    /**
     * @var Diff
     */
    private $diff;

    /**
     * @var Request
     */
    private $requestA, $requestB;

    /**
     * @var Response
     */
    private $responseA, $responseB;

    /**
     * @param Diff $diff
     * @param Request $requestA
     * @param Request $requestB
     * @param Response $responseA
     * @param Response $responseB
     */
    public function __construct(
        Diff $diff,
        Request $requestA,
        Request $requestB,
        Response $responseA,
        Response $responseB
    ) {
        $this->diff = $diff;
        $this->requestA = $requestA;
        $this->requestB = $requestB;
        $this->responseA = $responseA;
        $this->responseB = $responseB;
    }

    /**
     * @return Diff
     */
    public function getDiff()
    {
        return $this->diff;
    }

    /**
     * @return Request
     */
    public function getRequestA()
    {
        return $this->requestA;
    }

    /**
     * @return Request
     */
    public function getRequestB()
    {
        return $this->requestB;
    }

    /**
     * @return Response
     */
    public function getResponseA()
    {
        return $this->responseA;
    }

    /**
     * @return Response
     */
    public function getResponseB()
    {
        return $this->responseB;
    }
}
