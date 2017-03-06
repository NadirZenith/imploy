<?php

namespace AppBundle\EventListener;

use AppBundle\Controller\SecureControllerInterface;
use AppBundle\Model\DeployPayload;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class GitListener
{

    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        if (!is_array($controller) || !$controller[0] instanceof SecureControllerInterface) {
            return;
        }

        $request = $event->getRequest();

        $payload = new DeployPayload();
//        if (strtok($request->headers->get('user-agent'), '/') === 'GitHub-Hookshot') {
        if ($request->attributes->has('github_payload')) {
            $this->buildGitPayload($payload, $request->attributes->get('github_payload'));
        } else {
            $this->buildHeadersPayload($payload, $request->headers->all());
        }
        $request->attributes->set('deployPayload', $payload);

    }

    private function buildGitPayload(DeployPayload $payload, $content)
    {
        if (isset($content['ref'])) {
            $payload->setBranch(basename($content['ref']));
        }
    }

    private function buildHeadersPayload($payload, $headers)
    {
//        dd(func_get_args());
    }
}