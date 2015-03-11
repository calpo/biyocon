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

        $diff = $this->sut->compare($requestA, $requestB);

        echo $diff->render();
        $this->assertFalse($diff->hasDifference());
        $this->assertEmpty($diff->render());
    }

    public function testReturningDifferenceInResponseBody()
    {
        $requestA = new Request();
        $requestA->setUrl(sprintf('http://%s:%d/jsonA.php', WEB_SERVER_HOST, WEB_SERVER_PORT));

        $requestB = new Request();
        $requestB->setUrl(sprintf('http://%s:%d/jsonB.php', WEB_SERVER_HOST, WEB_SERVER_PORT));

        $diff = $this->sut->compare($requestA, $requestB);

        //print_r($diff->countBodyDiff());
        //$html = $diff::wrapHtml($diff->render());
        //file_put_contents('./tmp.html', $html);

        $this->assertTrue($diff->hasDifference());
        $this->assertTrue($diff->hasDifferentBody());
        $this->assertNotEmpty($diff->render());
        $this->assertEquals(2, $diff->getBodySummary()['+']);
        $this->assertEquals(3, $diff->getBodySummary()['-']);
    }

    public function testReturningDifferenceInResponseStatus()
    {
        $requestA = new Request();
        $requestA->setUrl(sprintf('http://%s:%d/index.php', WEB_SERVER_HOST, WEB_SERVER_PORT));

        $requestB = new Request();
        $requestB->setUrl(sprintf('http://%s:%d/notFound.php', WEB_SERVER_HOST, WEB_SERVER_PORT));

        $diff = $this->sut->compare($requestA, $requestB);

        $this->assertTrue($diff->hasDifference());
        $this->assertTrue($diff->hasDifferentStatus());
        $this->assertNotEmpty($diff->render());
    }
}
