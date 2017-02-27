<?php

namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AjaxListener implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return array(
//            KernelEvents::REQUEST    => array(array('onKernelRequest', 15)),
            KernelEvents::CONTROLLER => array(array('onKernelController', 15)),
            KernelEvents::RESPONSE   => array(array('onKernelResponse', 15)),
        );
    }

//    public function onKernelRequest(GetResponseEvent $event)
//    {}

    public function onKernelController(FilterControllerEvent $event)
    {
        if (!$this->matchRequest($event)) {
            return;
        }

        if ($event->getRequest()->getMethod() === 'GET') {
            // allow concurrent session before controller(does not work on REQUEST event)
            $event->getRequest()->getSession()->save();
        }
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (
            !$this->matchRequest($event) ||
            // ignore json responses
            $event->getResponse() instanceof JsonResponse
        ) {
            return;
        }

        $data = array(
            'content' => $event->getResponse()->getContent()
        );

        $status_code = $event->getResponse()->getStatusCode();
        if ($event->getResponse()->isRedirection()) {
            $status_code = 278;
            $data['location'] = $event->getResponse()->headers->get('Location');
        }

        $event->setResponse(
            new JsonResponse($data, $status_code, array('x-app-ajax' => true))
        );

    }


    private function matchRequest($event)
    {
        return
            // only flagged requests
            $event->getRequest()->headers->has('x-app-ajax') &&
            //only master requests
            $event->isMasterRequest();
    }
}