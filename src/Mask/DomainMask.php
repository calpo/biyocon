<?php
/**
 * This file is part of the biyocon.
 */

namespace Biyocon\Mask;


use Biyocon\Http\Request;
use Biyocon\Http\Response;

class DomainMask implements Mask
{
    const MASK = 'MASKED_DOMAIN';

    /**
     * @param string $text text to convert
     * @param Request $request
     * @param Response $response
     * @return string  converted text
     */
    public function mask($text, Request $request, Response $response)
    {
        $hostPattern = preg_quote($request->getHost());
        $pattern = "#{$hostPattern}#";

        if ($request->getPort()) {
            $port = $request->getPort();
            $pattern = "#{$hostPattern}(:$port)?#";
        }

        return preg_replace($pattern, static::MASK, $text);
    }
}
