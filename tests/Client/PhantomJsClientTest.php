<?php
/**
 * This file is part of the biyocon.
 */

namespace Biyocon\Test\Client;


use Biyocon\Client\PhantomJsClient;
use Biyocon\Http\Request;

class PhantomJsClientTest extends \PHPUnit_Framework_TestCase
{
    public function testClietReturnsResponseObject()
    {
        $request = new Request();
        $request->setUrl(sprintf('http://%s:%d/index.php', WEB_SERVER_HOST, WEB_SERVER_PORT));

        $client = new PhantomJsClient();
        $response = $client->send($request);

        $this->assertSame(200, $response->getStatus());
        $this->assertNotEmpty($response->getHeaders());
        $this->assertNotEmpty($response->getBody());
        $this->assertFileExists($response->getScreenShot());
    }

    public function testClietReturnsResponseObjectWhenNotFound()
    {
        $request = new Request();
        $request->setUrl(sprintf('http://%s:%d/no_file_exists.php', WEB_SERVER_HOST, WEB_SERVER_PORT));

        $client = new PhantomJsClient();
        $response = $client->send($request);

        $this->assertSame(404, $response->getStatus());
        $this->assertNotEmpty($response->getHeaders());
    }
}
