<?php
/**
 * This file is part of the biyocon.
 */

namespace Biyocon\Test\Mask;


use Biyocon\Http\Request;
use Biyocon\Http\Response;
use Biyocon\Mask\NoCacheQueryMask;

class NoCacheQueryMaskTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Biyocon\Mask\NoCacheQueryMask
     */
    private $sut;

    protected function setUp()
    {
        $this->sut = new NoCacheQueryMask();
    }

    /**
     * @dataProvider provideMaskingTestCase
     * @param string $target
     * @param string $expected
     */
    public function testMaskNoCacheQueryString($target, $expected)
    {
        $request = new Request();
        $response = new Response();

        $this->assertEquals($expected, $this->sut->mask($target, $request, $response));
    }

    public function provideMaskingTestCase()
    {
        $masked = NoCacheQueryMask::MASK;

        return [
            [
                '<img src="http://example.com/foo/bar.jpg?1234567">',
                "<img src=\"http://example.com/foo/bar.jpg?$masked\">",
            ], [
                '<img src="http://example.com/foo/bar.png?1234567">',
                "<img src=\"http://example.com/foo/bar.png?$masked\">",
            ], [
                '<img src="http://example.com/foo/bar.gif?1234567">',
                "<img src=\"http://example.com/foo/bar.gif?$masked\">",
            ], [
                '<script src="http://example.com/foo/bar.js?1234567"></script>',
                "<script src=\"http://example.com/foo/bar.js?$masked\"></script>",
            ], [
                '<link href="http://example.com/foo/bar.css?1234567">',
                "<link href=\"http://example.com/foo/bar.css?$masked\">",
            ],
        ];
    }
}
