<?php

namespace AppBundle\EventListener;

use AppBundle\Controller\RemoteUserControllerInterface;
use AppBundle\Controller\TokenAuthenticatedController;
use AppBundle\Entity\FieldsGroup;
use AppBundle\Entity\User;
use AppBundle\Services\ManagerFilter;
use AppBundle\Services\RemoteUserManagerInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AuthorizationCheckerListener
{

    /**
     * @var Registry
     */
    private $doctrine;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;
    /**
     * @var RemoteUserManagerInterface
     */
    private $userManager;

    /**
     * AuthorizationCheckerListener constructor.
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param Registry $doctrine
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker, Registry $doctrine, RemoteUserManagerInterface $userManager)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->doctrine = $doctrine;
        $this->userManager = $userManager;
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {

        $controller = $event->getController();
        if (!$controller[0] instanceof RemoteUserControllerInterface) {
            return;
        }

        $country = $event->getRequest()->attributes->get('country');
        $role = $this->getRole($country);
        if (!$role) {
            throw new AccessDeniedHttpException('This country does not exist');
        }

        // allow admin to see all fields
        // if ($this->authorizationChecker->isGranted(User::ROLE_SUPER_ADMIN)) {
        //      $role->setFields(array_keys($this->userManager->getAvailableFormFields()));
        // } else
        if (!$this->authorizationChecker->isGranted($role->getName())) {
            throw new AccessDeniedHttpException('This action needs a valid role!');
        }
        $managerFilter = new ManagerFilter($role);
        $event->getRequest()->attributes->set('managerFilter', $managerFilter);
    }

    /**
     * @param $country
     * @return object
     */
    private function getRole($country)
    {
        return $this->doctrine->getManager()->find(FieldsGroup::class, $country);
    }

//    /**
//     * @param GetResponseForExceptionEvent $event
//     */
//    public function onKernelException(GetResponseForExceptionEvent $event)
//    {
//        $controller = $event->getController();
//        $request = $event->getRequest();
//        if (!$controller[0] instanceof RemoteUserControllerInterface) {
//            return;
//        }
//
//        $request->getSession()->getFlashbag();
//
//
//    }
}