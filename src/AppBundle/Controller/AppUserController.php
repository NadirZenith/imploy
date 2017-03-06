<?php

namespace AppBundle\Controller;

use AppBundle\Entity\FieldsGroup;
use AppBundle\Entity\User;
use AppBundle\EventListener\AppEvents;
use AppBundle\Form\AppUserType;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/config/users")
 */
class AppUserController extends Controller
{

    /**
     * @Route("/list")
     */
    public function listAction(Request $request)
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return $this->render('user/list.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/create")
     */
    public function createAction(Request $request)
    {
        $user = new User();
        $form = $this->getForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', 'app.user.saved');
            $this->log("{agent} {action} {subject} \n {data}", array('action' => AppEvents::APP_USER_CREATE, 'subject' => $user));

            return $this->redirectToRoute('app_appuser_update', array('id' => $user->getId()));
        }

        // new | error
        return $this->render('user/form.html.twig', [
            'form'   => $form->createView(),
            'action' => 'create'
        ]);
    }

    /**
     * @Route("/{id}/read")
     */
    public function readAction(User $user, Request $request)
    {
    }

    /**
     * @Route("/{id}/update")
     */
    public function updateAction(User $user, Request $request)
    {
        $form = $this->getForm($user);


        $form->handleRequest($request);
        $user = $form->getData();

        // delete
        if ($form->get('delete')->isClicked()) {
            return $this->deleteAction($user, $request);

        } // update
        elseif ($form->isSubmitted() && $form->isValid()) {

            $userManager = $this->container->get('fos_user.user_manager');
            $userManager->updatePassword($user);
            $this->getDoctrine()->getManager()->flush();

            $request->getSession()->getFlashBag()->add('success', 'app.user.updated');
            $this->log("{agent} {action} {subject} \n {data}", array('action' => AppEvents::APP_USER_UPDATE, 'subject' => $user));

            return $this->redirect($request->headers->get('referer'));
        }

        // disable non admins self removal
        if (!$form->isSubmitted() && is_a($this->getUser(), User::class) && $this->getUser()->getId() === $user->getId() && !$this->isGranted(User::ROLE_SUPER_ADMIN)) {
            $form->remove('delete');
        }

        // edit
        return $this->render('user/form.html.twig', [
            'form'   => $form->createView(),
            'action' => 'update'
        ]);
    }

    /**
     * @Route("/{id}/delete")
     */
    public function deleteAction(User $user, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        $request->getSession()->getFlashBag()->add('success', 'app.user.deleted');
        $this->log("{agent} {action} {subject} \n {data}", array('action' => AppEvents::APP_USER_DELETE, 'subject' => $user));

        return $this->redirectToRoute('app_appuser_list');
    }


    private function getForm(User $user)
    {

        $form = $this->createForm(AppUserType::class, $user, array(
            'security.authorization_checker' => $this->get('security.authorization_checker')
        ));

        return $form;
    }


    private function log($msg, $context)
    {
        $this->get('logger')->info(sprintf("AppUserController: %s", $msg), $context);
    }
}
