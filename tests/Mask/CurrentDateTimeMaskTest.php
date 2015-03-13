<?php
/**
 * This file is part of the biyocon.
 */

namespace Biyocon\Test\Mask;


use Biyocon\Http\Request;
use Biyocon\Http\Response;
use Biyocon\Mask\CurrentDateTimeMask;

class CurrentDateTimeMaskTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CurrentDateTimeMask
     */
    private $sut;

    protected function setUp()
    {
        $this->sut = new CurrentDateTimeMask();
        $this->sut->setTime(strtotime('2015-01-23 12:34:56'));
    }

    /**
     * @dataProvider provideMaskingTimeTestCase
     * @param string $target
     * @param string $expected
     */
    public function testMaskingOnlyAroundCurrentTime($target, $expected)
    {
        $request = new Request();
        $response = new Response();

        $this->assertEquals($expected, $this->sut->mask($target, $request, $response));
    }

    public function provideMaskingTimeTestCase()
    {
        $formats = [
            'ATOM',
            'COOKIE',
            'ISO8601',
            'RFC822',
            'RFC850',
            'RFC1123',
        ];
        $case = [];
        foreach ($formats as $format) {
            $case = array_merge(
                $case,
                $this->getTestCaseByDateFormat(constant("DATE_{$format}"), "MASKED_{$format}")
            );
        }

        return $case;
    }

    private function getTestCaseByDateFormat($format, $mask)
    {
        $timestamp = strtotime('2015-01-23 12:34:56');

        return [
            [
                ' ' . date($format, $timestamp - 10) .' ',
                " $mask ",
            ], [
                ' ' . date($format, $timestamp + 10) .' ',
                " $mask ",
            ], [
                ' ' . date($format, $timestamp - 10000) .' ',
                ' ' . date($format, $timestamp - 10000) .' ',
            ], [
                ' ' . date($format, $timestamp + 10000) .' ',
                ' ' . date($format, $timestamp + 10000) .' ',
            ],
        ];
    }
}
