<?php
/**
 * This file is part of the biyocon.
 */

namespace Biyocon\Test\ResultBuilder;

use Biyocon\Comparator\Crawler;
use Biyocon\Http\Request;
use Biyocon\ResultBuilder\HtmlBuilder;

class HtmlBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HtmlBuilder
     */
    private $sut;

    protected function setUp()
    {
        $baseDirectory = sys_get_temp_dir();
        $subDirectory = uniqid('biyocon.HtmlBuilderTest.');
        $this->sut = new HtmlBuilder($baseDirectory, $subDirectory);
        $this->sut->initialize();
    }

    public function testBuildingResultHtml()
    {
        $this->sut->add($this->crawlResult('htmlA.php', 'htmlA.php'));
        $this->sut->add($this->crawlResult('htmlA.php', 'htmlB.php'));
        $this->sut->add($this->crawlResult('htmlA.php', 'jsonA.php'));

        $this->sut->build();

        echo $this->sut->getDirectory();
        exec('open ' . $this->sut->getDirectory());
        $this->markTestIncomplete();
    }

    private function crawlResult($fileA, $fileB)
    {
        $requestA = new Request();
        $requestA->setUrl(sprintf('http://%s:%d/%s', WEB_SERVER_HOST, WEB_SERVER_PORT, $fileA));

        $requestB = new Request();
        $requestB->setUrl(sprintf('http://%s:%d/%s', WEB_SERVER_HOST, WEB_SERVER_PORT, $fileB));

        $crawler = new Crawler();

        return $crawler->compare($requestA, $requestB);
    }
}
