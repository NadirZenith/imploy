<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Controller\SecurityController as FOSSecurityController;

class SecurityController extends FOSSecurityController
{

    public function loginAction(Request $request)
    {
        $this->get('profiler')->disable();

        return parent::loginAction($request);
    }

    /**
     * @Route("/profile")
     */
    public function profileAction(Request $request)
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createNotFoundException();
        }

        $response = $this->forward('AppBundle:AppUser:update', array(
            'request' => $request,
            'user'    => $user
        ));

        // update session locale
        $request->getSession()->set('_locale', $user->getLocale());

        return $response;

    }
}
