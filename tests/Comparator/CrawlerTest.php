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

    public function testNotingToBeRenderedWithSameResponse()
    {
        $requestA = new Request();
        $requestA->setUrl(sprintf('http://%s:%d/index.php', WEB_SERVER_HOST, WEB_SERVER_PORT));

        $requestB = new Request();
        $requestB->setUrl(sprintf('http://%s:%d/index.php', WEB_SERVER_HOST, WEB_SERVER_PORT));

        $result = $this->sut->compare($requestA, $requestB);

        $this->assertFalse($result->getDiff()->hasDifference());
        $this->assertEmpty($result->getDiff()->render());
    }

    public function testReturningDifferenceInResponseBody()
    {
        $requestA = new Request();
        $requestA->setUrl(sprintf('http://%s:%d/jsonA.php', WEB_SERVER_HOST, WEB_SERVER_PORT));

        $requestB = new Request();
        $requestB->setUrl(sprintf('http://%s:%d/jsonB.php', WEB_SERVER_HOST, WEB_SERVER_PORT));

        $result = $this->sut->compare($requestA, $requestB);

        //print_r($result->getDiff()->countBodyDiff());
        //$html = $result->getDiff()::wrapHtml($result->getDiff()->render());
        //file_put_contents('./tmp.html', $html);

        $this->assertTrue($result->getDiff()->hasDifference());
        $this->assertTrue($result->getDiff()->hasDifferentBody());
        $this->assertNotEmpty($result->getDiff()->render());
        $this->assertEquals(2, $result->getDiff()->getBodySummary()['+']);
        $this->assertEquals(3, $result->getDiff()->getBodySummary()['-']);
    }

    public function testReturningDifferenceInResponseStatus()
    {
        $requestA = new Request();
        $requestA->setUrl(sprintf('http://%s:%d/index.php', WEB_SERVER_HOST, WEB_SERVER_PORT));

        $requestB = new Request();
        $requestB->setUrl(sprintf('http://%s:%d/notFound.php', WEB_SERVER_HOST, WEB_SERVER_PORT));

        $result = $this->sut->compare($requestA, $requestB);

        $this->assertTrue($result->getDiff()->hasDifference());
        $this->assertTrue($result->getDiff()->hasDifferentStatus());
        $this->assertNotEmpty($result->getDiff()->render());
    }
}
