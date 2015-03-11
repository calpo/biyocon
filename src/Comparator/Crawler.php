<?php
/**
 * This file is part of the biyocon.
 */

namespace Biyocon\Comparator;


use Biyocon\Client\Client;
use Biyocon\Client\PhantomJsClient;
use Biyocon\Http\Request;

class Crawler
{
    /**
     * constructor
     *
     * @param Client $client
     */
    public function __construct (Client $client = null)
    {
        $this->client = new PhantomJsClient();
    }

    /**
     * Crawl pages, compare responses and return difference.
     *
     * @param Request $requestA
     * @param Request $requestB
     * @return Diff
     */
    public function compare (Request $requestA, Request $requestB)
    {
        $responseA = $this->client->send($requestA);
        $responseB = $this->client->send($requestB);

        $comparator = new ResponseComparator();

        return $comparator->compare($responseA, $responseB);
    }
}
