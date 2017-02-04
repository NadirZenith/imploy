<?php

namespace AppBundle\EventListener;

use AppBundle\Controller\SecureControllerInterface;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class GitListener
{

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        return;
        if (!is_array($controller) || !$controller[0] instanceof SecureControllerInterface) {
            return;
        }

        $request = $event->getRequest();
        d($request->headers->all());
        $HTTP_X_HUB_SIGNATURE = $request->headers->get('HTTP_X_HUB_SIGNATURE');
        $HTTP_CONTENT_TYPE = $request->headers->get('HTTP_CONTENT_TYPE');
        $HTTP_X_GITHUB_EVENT = $request->headers->get('HTTP_X_GITHUB_EVENT');

        dd($HTTP_X_HUB_SIGNATURE, $HTTP_CONTENT_TYPE, $HTTP_X_GITHUB_EVENT);
    }
}