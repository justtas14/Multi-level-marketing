<?php
/**
 * Created by PhpStorm.
 * User: marius
 * Date: 2018-06-14
 * Time: 15:31
 */

namespace App\Util;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class JsonRequestListener
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if ($request->getContentType() == 'json') {
            $data = $request->getContent();
            if (!$data || $data == '{}' || $data == '[]') {
                $request->request->replace([]);
            } else {
                $newData = json_decode($data, true);
                if ($newData) {
                    $request->request->replace($newData);
                } else {
                    $event->setResponse(new Response('Bad JSON format', 400));
                }
            }
        }
    }
}
