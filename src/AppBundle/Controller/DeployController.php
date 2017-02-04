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
        $dir = $this->getParameter('kernel.root_dir') . '/../web/';
        $filename = 'test.html';
        $file = $dir . $filename;

        ob_start();
        d($request->headers->all());

        $HTTP_X_HUB_SIGNATURE = $request->headers->get('HTTP_X_HUB_SIGNATURE');
        $HTTP_CONTENT_TYPE = $request->headers->get('HTTP_CONTENT_TYPE');
        $HTTP_X_GITHUB_EVENT = $request->headers->get('HTTP_X_GITHUB_EVENT');
        d($HTTP_X_HUB_SIGNATURE, $HTTP_CONTENT_TYPE, $HTTP_X_GITHUB_EVENT);

        d($request->getContent());

        $content = ob_get_clean();
        file_put_contents($file, $content, FILE_APPEND | LOCK_EX);

        return new Response('working');
    }

    /**
     * @Route("/")
     */
    public function defaultAction(Request $request)
    {

        return $this->render('AppBundle:DeployController:main.html.twig', array(// ...
        ));
    }

}
