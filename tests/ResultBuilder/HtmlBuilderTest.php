<?php
/**
 * This file is part of the biyocon.
 */

namespace Biyocon\Test\ResultBuilder;

use Biyocon\Comparator\Crawler;
use Biyocon\Http\Request;
use Biyocon\ResultBuilder\HtmlBuilder;
use Biyocon\Util\Util;

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

    protected function tearDown()
    {
        //exec('open ' . $this->sut->getDirectory());
        Util::deleteDirectory($this->sut->getDirectory());
    }

    public function testBuildingResultHtml()
    {
        $this->sut->add($this->crawlResult('htmlA.php', 'htmlA.php'));
        $this->sut->add($this->crawlResult('htmlA.php', 'htmlB.php'));
        $this->sut->add($this->crawlResult('htmlA.php', 'jsonA.php'));
        $this->sut->add($this->crawlResult('htmlA.php', 'notFound.php'));

        //$this->sut->add($this->crawlExternalResult('http://oshiete.goo.ne.jp/', 'http://oshiete.goo.ne.jp/'));
        //$this->sut->add($this->crawlExternalResult('http://oshiete.goo.ne.jp/qa/123456.html', 'http://oshiete.goo.ne.jp/qa/123456.html'));
        //$this->sut->add($this->crawlExternalResult('http://oshiete.goo.ne.jp/watcher/', 'http://oshiete.goo.ne.jp/watcher/'));
        //$this->sut->add($this->crawlExternalResult('http://oshiete.goo.ne.jp/qa/9999999.html', 'http://oshiete.goo.ne.jp/qa/9999999.html'));
        //$this->sut->add($this->crawlExternalResult('http://oshiete.goo.ne.jp/api/category', 'http://oshiete.goo.ne.jp/api/category'));
        //$this->sut->add($this->crawlExternalResult('http://oshiete.goo.ne.jp/rd/hoge/fuga/?http://oshiete.goo.ne.jp/', 'http://oshiete.goo.ne.jp/rd/hoge/fuga/?http://oshiete.goo.ne.jp/'));

        $this->sut->build();

        $directory = $this->sut->getDirectory();
        $this->assertFileExists($this->sut->getHtmlFile());
        $this->assertFileExists("$directory/result.css");
        $this->assertFileExists("$directory/php-diff-style.css");
        $this->assertFileExists("$directory/application.js");
        $this->assertFileExists("$directory/jquery.js");
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

    private function crawlExternalResult($urlA, $urlB)
    {
        $requestA = new Request();
        $requestA->setUrl($urlA);

        $requestB = new Request();
        $requestB->setUrl($urlB);

        $crawler = new Crawler();

        return $crawler->compare($requestA, $requestB);
    }
}
