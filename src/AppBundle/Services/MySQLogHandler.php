<?php

namespace AppBundle\Services;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use Symfony\Bridge\Doctrine\RegistryInterface;
use AppBundle\Entity\Log;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * This class is a handler for Monolog, which can be used
 * to write records in a MySQL table
 */
class MySQLogHandler extends AbstractProcessingHandler
{
    /**
     * @var RegistryInterface
     */
    protected $doctrine;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    private $log_placeholder = "{agent} {action} {subject} \n {data}";
    /**
     * @var
     */
    private $log_class;


    /**
     * Constructor of this class, sets the PDO and calls parent constructor
     *
     * @param RegistryInterface $doctrine
     * @param TokenStorageInterface $tokenStorage
     * @param bool|int $level Debug level which this handler should store
     * @param bool $bubble
     * @param $log_class
     */
    public function __construct(RegistryInterface $doctrine, TokenStorageInterface $tokenStorage, $level = Logger::DEBUG, $bubble = true, $log_class = Log::class)
    {
        parent::__construct($level, $bubble);

        $this->doctrine = $doctrine;
        $this->tokenStorage = $tokenStorage;

        $this->log_class = $log_class;
    }


    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param  $record []
     * @return void
     */
    protected function write(array $record)
    {
        $context = $record['context'];

        if (!isset($context['agent'])) {
            // set agent to current logged in user
            $context['agent'] = $this->getObjectRef($this->getCurrentUser());
        }

        // if the subject exists and is an object serialize it to array
        if (!isset($context['data']) && isset($context['subject']) && is_object($context['subject'])) {
            // $context['data'] = $this->getObjectRef($context['subject']);
            $context['data'] = print_r((array)$context['subject'], true);
        }

        $message = $this->interpolate($record['message'], $context);

        $log = new  $this->log_class($message, $record['datetime'], $context);
        $log->setChannel($record['channel']);
        $log->setLevel($record['level']);

        $this->getEntityManager()->persist($log);
        $this->getEntityManager()->flush($log);

    }

    private function interpolate($message, array $context = array())
    {
        // build a replacement array with braces around the context keys
        $replace = array();
        foreach ($context as $key => $val) {
            // check that the value can be casted to string
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }

        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }

    private function getEntityManager()
    {
        return $this->doctrine->getManagerForClass($this->log_class);
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
}