<?php
/**
 * This file is part of the biyocon.
 */

namespace Biyocon\Client;


use Biyocon\Http\Request;
use Biyocon\Http\Response;
use JonnyW\PhantomJs;

class PhantomJsClient implements Client
{
    /**
     * @param Request $request
     * @return Response
     */
    public function send(Request $request)
    {
        $response = new Response();
        $imageFile = sys_get_temp_dir() . '/biyocon-pjclient-' . uniqid() . '.jpg';

        $pjClient = PhantomJs\Client::getInstance();
        $pjClient->setBinDir(__DIR__ . '/../../bin');
        $pjClient->addOption('--ignore-ssl-errors=true');

        $pjRequest = $pjClient->getMessageFactory()->createCaptureRequest($request->getUrl(), $request->getMethod());
        $pjRequest->setCaptureFile($imageFile);

        $pjResponse = $pjClient->getMessageFactory()->createResponse();

        $pjClient->send($pjRequest, $pjResponse);

        $response->setScreenShot($imageFile);
        unlink($imageFile);
        $response->setStatus($pjResponse->getStatus());
        $response->setHeadersByHash($pjResponse->getHeaders());
        $response->setBody($pjResponse->getContent());

        return $response;
    }
}
