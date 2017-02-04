<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DeployControllerController extends Controller
{
    /**
     * @Route("/")
     */
    public function mainAction()
    {
        return $this->render('AppBundle:DeployController:main.html.twig', array(
            // ...
        ));
    }

}
