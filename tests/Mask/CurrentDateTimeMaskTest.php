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
            [DATE_ATOM, 'MASKED_ATOM'],
            [DATE_COOKIE, 'MASKED_COOKIE'],
            [DATE_ISO8601, 'MASKED_ISO8601'],
            [DATE_RFC822, 'MASKED_RFC822'],
            [DATE_RFC850, 'MASKED_RFC850'],
            [DATE_RFC1123, 'MASKED_RFC1123'],
            ['D, d M Y H:i:s T', 'MASKED_HTTP'],
        ];
        $case = [];
        foreach ($formats as $format) {
            $case = array_merge(
                $case,
                $this->getTestCaseByDateFormat($format[0], $format[1])
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
