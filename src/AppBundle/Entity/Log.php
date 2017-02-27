<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Log
 *
 * @ORM\Table(name="log")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\LogRepository")
 */
class Log
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $channel;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $level;

    /**
     * @ORM\Column(type="datetime")
     */
    private $time;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $action;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $message;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logGroup;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subject;

    public function __construct($message = null, \DateTime $time = null, array $context = array())
    {
        if (null === $time) {
            $time = new \DateTime();

        }
        $this->setTime($time);
        $this->setMessage($message);
        $this->setContext($context);
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set channel
     *
     * @param string $channel
     * @return Log
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * Get channel
     *
     * @return string
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * Set level
     *
     * @param string $level
     * @return Log
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set log message
     *
     * @param $message
     * @return Log
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get log message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Log
     */
    public function setTime($created)
    {
        $this->time = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set user
     *
     * @param string $user
     *
     * @return Log
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set action
     *
     * @param string $action
     *
     * @return Log
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set logGroup
     *
     * @param string $logGroup
     *
     * @return Log
     */
    public function setLogGroup($logGroup)
    {
        $this->logGroup = $logGroup;

        return $this;
    }

    /**
     * Get logGroup
     *
     * @return string
     */
    public function getLogGroup()
    {
        return $this->logGroup;
    }

    /**
     * Set subject
     *
     * @param string $subject
     *
     * @return Log
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get Subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set Context
     *
     * @return Log
     */
    public function setContext(array $context = array())
    {
        if (!isset($context['action'])) {
            $context['action'] = ' - ';
        }

        $this->setAction($context['action']);

        if (isset($context['group'])) {
            $this->setLogGroup($context['group']);
        }

        if (isset($context['agent'])) {
            $this->setUser($context['agent']);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getTime()->format('Y/m/d h:s:i');
    }
}
