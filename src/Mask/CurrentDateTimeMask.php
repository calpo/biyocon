<?php
/**
 * This file is part of the biyocon.
 */

namespace Biyocon\Mask;


use Biyocon\Http\Request;
use Biyocon\Http\Response;

class CurrentDateTimeMask implements Mask
{
    /**
     * Margin to current time. (sec)
     */
    const MARGIN = 60;

    private $time;

    public function __construct()
    {
        $this->time = time();
    }

    /**
     * @param string $text text to convert
     * @param Request $request
     * @param Response $response
     * @return string  converted text
     */
    public function mask($text, Request $request, Response $response)
    {
        foreach ($this->getDateTimePatterns() as $pattern) {
            $text = $this->replaceDateTime($text, $pattern['pattern'], $pattern['mask']);
        }

        return $text;
    }

    /**
     * @param int $time  timestamp
     */
    public function setTime($time)
    {
        $this->time = (int)$time;
    }

    protected function getDateTimePatterns()
    {
        $ymd = '[0-9]{4}-[0-9]{2}-[0-9]{2}';
        $his = '[0-9]{2}:[0-9]{2}:[0-9]{2}';
        $weekdays = '(Sunday|Monday|Tuesday|Wednesday|Thursday|Friday|Saturday)';
        $shortWeekdays = '(Sun|Mon|Tue|Wed|Thu|Fri|Sat)';
        $months = '(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)';
        $zone = '[A-Z]{3}';
        $gtmDiff = '\+[0-9]{4}';
        $dmy4 = "[0-9]{2}-{$months}-[0-9]{4}";
        $dmy2 = "[0-9]{2}-{$months}-[0-9]{2}";
        $dmy4s = "[0-9]{2} {$months} [0-9]{4}";
        $dmy2s = "[0-9]{2} {$months} [0-9]{2}";

        return [
            [
                'pattern' => "/($ymd $his)/",
                'mask' => 'DATETIME',
            ], [
                'pattern' => "/({$ymd}T{$his}\\+[0-9]{2}:[0-9]{2})/",
                'mask' => 'ATOM',
            ], [
                'pattern' => "/({$weekdays}, {$dmy4} {$his} {$zone})/",
                'mask' => 'COOKIE',
            ], [
                'pattern' => "/({$ymd}T{$his}{$gtmDiff})/",
                'mask' => 'ISO8601',
            ], [
                'pattern' => "/({$shortWeekdays}, {$dmy2s} {$his} {$gtmDiff})/",
                'mask' => 'RFC822',
            ], [
                'pattern' => "/({$weekdays}, {$dmy2} {$his} {$zone})/",
                'mask' => 'RFC850',
            ], [
                'pattern' => "/({$shortWeekdays}, {$dmy4s} {$his} {$gtmDiff})/",
                'mask' => 'RFC1123',
            ],
        ];
    }

    private function replaceDateTime($text, $pattern, $mask)
    {
        $text = preg_replace_callback($pattern, function ($matches) use ($mask) {
            $timestamp = strtotime($matches[1]);
            if (abs($this->time - $timestamp) <= static::MARGIN) {
                return "MASKED_{$mask}";
            }
            return $matches[1];
        }, $text);

        return $text;
    }
}
