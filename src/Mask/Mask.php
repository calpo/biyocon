<?php
/**
 * This file is part of the biyocon.
 */

namespace Biyocon\Mask;


use Biyocon\Http\Request;
use Biyocon\Http\Response;

interface Mask
{
    /**
     * @param string $text  text to convert
     * @param Request $request
     * @param Response $response
     * @return string  converted text
     */
    public function mask($text, Request $request, Response $response);
}
