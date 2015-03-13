<?php
/**
 * This file is part of the biyocon.
 */

namespace Biyocon\Comparator;


use Biyocon\Client\Client;
use Biyocon\Client\PhantomJsClient;
use Biyocon\Http\Request;
use Biyocon\Mask\CurrentDateTimeMask;
use Biyocon\Mask\DomainMask;
use Biyocon\Mask\NoCacheQueryMask;

class Crawler
{
    /**
     * @var Client
     */
    private $client;

    /**
     * constructor
     *
     * @param Client $client
     */
    public function __construct (Client $client = null)
    {
        $this->client = $client ?: new PhantomJsClient();
    }

    /**
     * Crawl pages, compare responses and return difference.
     *
     * @param Request $requestA
     * @param Request $requestB
     * @return Result
     */
    public function compare (Request $requestA, Request $requestB)
    {
        $responseA = $this->client->send($requestA);
        $responseB = $this->client->send($requestB);

        $comparator = new ResponseComparator();


        // TODO 外部から設定
        $comparator->addHeaderMask(new DomainMask());
        $comparator->addHeaderMask(new CurrentDateTimeMask());
        $comparator->addBodyMask(new DomainMask());
        $comparator->addBodyMask(new CurrentDateTimeMask());
        $comparator->addBodyMask(new NoCacheQueryMask());


        $diff = $comparator->compare($requestA, $requestB, $responseA, $responseB);

        return new Result($diff, $requestA, $requestB, $responseA, $responseB);
    }
}
