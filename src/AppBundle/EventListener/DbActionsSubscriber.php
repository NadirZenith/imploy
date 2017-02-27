<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Log;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DbActionsSubscriber implements EventSubscriber
{
    private $log_placeholder = "{agent} {action} {subject} \n {data}";
    private $tokenStorage;
    private $logger;

    public function __construct(TokenStorageInterface $tokenStorage, LoggerInterface $logger)
    {
        $this->tokenStorage = $tokenStorage;
        $this->logger = $logger;
    }

    public function getSubscribedEvents()
    {
        return array(
            'postPersist',
            'postUpdate',
            // pre to avoid loosing entity id
            'preRemove'
        );
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->logAction($args, 'updated');
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->logAction($args, 'persisted');
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $this->logAction($args, 'removed');
    }

    public function logAction(LifecycleEventArgs $args, $action)
    {
        $entity = $args->getEntity();

        // prevent recursion
        if ($entity instanceof Log) {
            return;
        }

        $context = array(
            'agent'   => $this->getObjectRef($this->getCurrentUser()),
            'action'  => $action,
            'subject' => $this->getObjectRef($entity),
            'data'    => print_r((array)$entity, true)
//            'data'    => $action === 'removed' ? print_r((array)$entity, true) : ''
        );

        $this->getLogger()->info($this->log_placeholder, $context);
    }

    private function getCurrentUser()
    {
        return $this->tokenStorage->getToken() ?
            $this->tokenStorage->getToken()->getUser() :
            false;
    }

    private function getObjectRef($object)
    {
        return $object ? sprintf('%s::%s', get_class($object), $this->getObjectId($object)) : '';
    }

    private function getObjectId($object)
    {
        $rc = new \ReflectionClass(get_class($object));

        $id = 'n/a';
        if ($rc->hasMethod('getId')) {
            $id = $object->getId();
        } elseif ($rc->hasMethod('__toString')) {
            $id = $object->__toString();
        }

        return $id;
    }

    private function getLogger()
    {
        return $this->logger;
    }
}