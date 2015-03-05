<?php
/**
 * This file is part of the biyocon.
 */

namespace Biyocon\Client;


use Biyocon\Http\Request;
use Biyocon\Http\Response;

interface Client
{
    /**
     * @param Request $request
     * @return Response
     */
    public function send(Request $request);
}
