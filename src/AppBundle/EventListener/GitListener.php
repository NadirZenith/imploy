<?php

namespace AppBundle\EventListener;

use AppBundle\Controller\SecureControllerInterface;
use AppBundle\Model\DeployPayload;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class GitListener
{

    public function onKernelController(FilterControllerEvent $event)
//    public function onKernelController(GetResponseEvent $event)
    {
        $controller = $event->getController();
        if (!is_array($controller) || !$controller[0] instanceof SecureControllerInterface) {
            return;
        }

        $request = $event->getRequest();

        $payload = new DeployPayload();

        $request->attributes->set('deployPayload', $payload);

    }
}