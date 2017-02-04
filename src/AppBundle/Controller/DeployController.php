<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class DeployController extends Controller
{
    /**
     * @Route("/")
     */
    public function mainAction(Request $request )
    {   
	$dir = $this->getParameter('kernel.root_dir') . '/../web/';
	$filename = 'test.html';
	$file = $dir . $filename;

	ob_start();	
	d($request);
	$content = ob_get_clean();

	file_put_contents($file, $content, FILE_APPEND | LOCK_EX);
	
        return $this->render('AppBundle:DeployController:main.html.twig', array(
            // ...
        ));
    }

}
