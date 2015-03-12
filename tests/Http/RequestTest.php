<?php
/**
 * This file is part of the biyocon.
 */

namespace Biyocon\Test\Http;


use Biyocon\Http\Method;
use Biyocon\Http\Request;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Request
     */
    private $sut;

    public function setUp()
    {
        $this->sut = new Request();
    }

    /**
     * @dataProvider provideUrlTestCase
     */
    public function testUrlPartsCanBeReplaced($method, $param, $expect)
    {
        $this->sut->setUrl('http://example.com/foo?a=1#bar');
        $this->sut->$method($param);

        $this->assertEquals($expect, $this->sut->getUrl());
    }

    public function provideUrlTestCase()
    {
        return [
            ['setMethod', Method::POST, 'http://example.com/foo#bar'],
            ['setScheme', 'https', 'https://example.com/foo?a=1#bar'],
            ['setHost', 'example.co.jp', 'http://example.co.jp/foo?a=1#bar'],
            ['setUser', 'user', 'http://user:example.com/foo?a=1#bar'],
            ['setPass', 'pass', 'http://pass@example.com/foo?a=1#bar'],
            ['setPort', '8080', 'http://example.com:8080/foo?a=1#bar'],
            ['setPath', '/buz', 'http://example.com/buz?a=1#bar'],
            ['setData', ['b'=>2], 'http://example.com/foo?b=2#bar'],
            ['setFragment', 'gux', 'http://example.com/foo?a=1#gux'],
        ];
    }
}
