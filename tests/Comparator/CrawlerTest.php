<?php
/**
 * This file is part of the biyocon.
 */

namespace Biyocon\Test\Comparator;


use Biyocon\Comparator\Crawler;
use Biyocon\Http\Request;

class CrawlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Crawler
     */
    private $sut;

    public function setUp()
    {
        $this->sut = new Crawler();
    }

    //public function testComparingSameResponseReturnsZeroDifference()
    public function xxxxComparingSameResponseReturnsZeroDifference()
    {
        $requestA = new Request();
        $requestA->setUrl(sprintf('http://%s:%d/index.php', WEB_SERVER_HOST, WEB_SERVER_PORT));

        $requestB = new Request();
        $requestB->setUrl(sprintf('http://%s:%d/index.php', WEB_SERVER_HOST, WEB_SERVER_PORT));

        $diff = $this->sut->compare($requestA, $requestB);

        print_r($diff);
        $this->markTestIncomplete('Diffクラス未実装');
    }

    public function testComparingDifferenceResponseHeader()
    {
        $requestA = new Request();
        $requestA->setUrl(sprintf('http://%s:%d/index.php', WEB_SERVER_HOST, WEB_SERVER_PORT));

        $requestB = new Request();
        $requestB->setUrl(sprintf('http://%s:%d/jsonA.php', WEB_SERVER_HOST, WEB_SERVER_PORT));

        $diff = $this->sut->compare($requestA, $requestB);

        print_r($diff);
        $this->markTestIncomplete('Diffクラス未実装');
    }
}
