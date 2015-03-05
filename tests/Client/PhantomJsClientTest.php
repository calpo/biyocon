<?php
/**
 * This file is part of the biyocon.
 */

namespace Biyocon\Test\Client;


use Biyocon\Client\PhantomJsClient;
use Biyocon\Http\Request;

class PhantomJsClientTest extends \PHPUnit_Framework_TestCase
{
    public function testSendingRequest()
    {
        $request = new Request();
        $request->setUrl('http://google.com/');

        $client = new PhantomJsClient();
        $response = $client->send($request);

        //echo $response->getStatus();
        //print_r($response->getHeaders());
        //echo $response->getBody();

        unset($response);
    }
}
