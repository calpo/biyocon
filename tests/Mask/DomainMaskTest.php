<?php
/**
 * This file is part of the biyocon.
 */

namespace Biyocon\Test\Mask;


use Biyocon\Http\Request;
use Biyocon\Http\Response;
use Biyocon\Mask\DomainMask;

class DomainMaskTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Biyocon\Mask\DomainMask
     */
    private $sut;

    protected function setUp()
    {
        $this->sut = new DomainMask();
    }

    /**
     * @dataProvider provideRequestUrls
     * @param string $requestUrl
     */
    public function testMaskDomainString($requestUrl, $target, $expected)
    {
        $request = new Request();
        $request->setUrl($requestUrl);

        $response = new Response();

        $this->assertEquals($expected, $this->sut->mask($target, $request, $response));
    }

    public function provideRequestUrls()
    {
        $masked = DomainMask::MASK;

        return [
            [
                'http://example.com/foo.html',
                'https://example.com/bar.html',
                "https://$masked/bar.html"
            ], [
                'http://example.com/foo.html',
                'https://example.com:8080/bar.html',
                "https://$masked:8080/bar.html"
            ], [
                'http://example.com:8080/foo.html',
                'https://example.com/bar.html',
                "https://$masked/bar.html"
            ], [
                'http://example.com:8080/foo.html',
                'https://example.com:8080/bar.html',
                "https://$masked/bar.html"
            ],
            [
                'http://user:pass@example.com/foo.html',
                'https://example.com/bar.html',
                "https://$masked/bar.html"
            ], [
                'http://user:pass@example.com/foo.html',
                'https://example.com:8080/bar.html',
                "https://$masked:8080/bar.html"
            ], [
                'http://user:pass@example.com:8080/foo.html',
                'https://example.com/bar.html',
                "https://$masked/bar.html"
            ], [
                'http://user:pass@example.com:8080/foo.html',
                'https://example.com:8080/bar.html',
                "https://$masked/bar.html"
            ],
        ];
    }
}
