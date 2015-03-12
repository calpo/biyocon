<?php
/**
 * This file is part of the biyocon.
 */

namespace Biyocon\Mask;


use Biyocon\Http\Request;
use Biyocon\Http\Response;

class NoCacheQueryMask implements Mask
{
    const MASK = 'MASKED_QUERY';

    /**
     * @param string $text text to convert
     * @param Request $request
     * @param Response $response
     * @return string  converted text
     */
    public function mask($text, Request $request, Response $response)
    {
        return preg_replace('/(\.(jpg|gif|png|js|css)\?)[-_.%a-zA-Z0-9]+/i', '$1'.static::MASK, $text);
    }
}
