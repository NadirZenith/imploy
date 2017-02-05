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
     * @Route("/payload")
     * @Method("POST")
     */
    public function postAction($githubPayload, Request $request)
    {


        $branch = basename($githubPayload['ref']);
        echo 'push ' . $branch;
//        dd($request, $githubPayload);
        if ('pre' == $branch) {

            exec('cd /srv/nzlab.es/pre && /usr/bin/git pull origin pre 2>&1', $output);
            $this->log("goto pre and do git pull");
            $this->log($output);

            exec('cd /srv/nzlab.es/pre && sh ./deploy/deploy.sh dev', $output);
            $this->log("goto pre and deploy dev");
            $this->log($output);

        }


//        d($this->isGranted('ROLE_SUPER_ADMIN'), $this->getUser());

//        switch ($request->headers->get('content-type')) {
//            case 'application/json':
//                $json = $request->getContent();
//                break;
//
//            case 'application/x-www-form-urlencoded':
//                $json = $request->request->get('payload');
//            default:
//                throw new \Exception(sprintf("Unsupported content type: '%s'", $request->headers->get('content-type')));
//                break;
//        }


//        $this->log(array(
//            $request->headers->get('content-type'),
//            $request->headers->get('x-github-event'),
//            $request->headers->get('x-hub-signature'),
//            $request->getContent()
//        ));

        return new Response('ok');
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
        echo date('d/m/y H:i:s');
        d($log);
        $content = ob_get_clean();

        file_put_contents($file, $content, FILE_APPEND | LOCK_EX);

    }

}
