<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeployController extends Controller implements SecureControllerInterface
{
    /**
     * @Route("/")
     * @Method("POST")
     */
    public function postAction(Request $request)
    {

        $CONTENT_TYPE = $request->headers->get('content-type');
        $X_HUB_SIGNATURE = $request->headers->get('x-hub-signature');
        $X_GITHUB_EVENT = $request->headers->get('x-github-event');

        $this->log(array($X_HUB_SIGNATURE, $CONTENT_TYPE, $X_GITHUB_EVENT, $request->getContent()));

        return new Response('working ...');
    }

    /**
     * @Route("/")
     */
    public function defaultAction(Request $request)
    {

        return $this->render('AppBundle:DeployController:main.html.twig', array(// ...
        ));
    }

    private function log($log)
    {

        $dir = $this->getParameter('kernel.root_dir') . '/../web/';
        $filename = 'log.html';
        $file = $dir . $filename;

        ob_start();
        echo date('d/m/y m:i:s');
        d($log);
        $content = ob_get_clean();

        file_put_contents($file, $content, FILE_APPEND | LOCK_EX);

    }

}
