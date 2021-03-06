<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User extends BaseUser implements EquatableInterface
{
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_DEPLOY = 'ROLE_DEPLOY';

    public static $AVAILABLE_ROLES = array(
        'user.role.user'        => self::ROLE_DEFAULT,
        'user.role.admin'       => self::ROLE_ADMIN,
        'user.role.super_admin' => self::ROLE_SUPER_ADMIN,
        // APP
        'user.role.deploy'      => self::ROLE_DEPLOY,
    );

    public static $AVAILABLE_LOCALES = array(
        'user.locale.en' => 'en',
        'user.locale.es' => 'es'
    );

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime")
     */
    protected $createdOn;

    /**
     * @var string
     * @ORM\Column(name="locale", type="string")
     */
    protected $locale = 'en';

    /**
     * @var string
     * @ORM\Column(name="github_username", type="string", nullable=true)
     */
    protected $githubUsername;

    /**
     * @var string
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Pipeline", inversedBy="users")
     * @ORM\JoinColumn(name="pipeline_id", referencedColumnName="id")
     */
    protected $pipeline;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->createdOn = new \DateTime('now');
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
     * Get createdOn
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * @param $createdOn
     * @return $this
     */
    public function setCreatedOn(\DateTime $createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale($locale = 'en')
    {
        $this->locale = $locale;
    }


    /**
     * @param UserInterface $user
     * @return bool
     */
    public function isEqualTo(UserInterface $user)
    {

        $isEqual = $user->isEnabled() == $this->isEnabled();
        return $isEqual;

        // @todo should serialize roles
        if ($user instanceof User) {
            // Check that the roles are the same, in any order
            $isEqual = count($this->getRoles()) == count($user->getRoles());
            if ($isEqual) {
                foreach ($this->getRoles() as $role) {
                    $isEqual = $isEqual && in_array($role, $user->getRoles());
                }
            }
            return $isEqual;
        }

        return false;

    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf("%s:%d", $this->getUsername(), $this->getId());
    }

    /**
     * @return string
     */
    public function getGithubUsername()
    {
        return $this->githubUsername;
    }

    /**
     * @param string $githubUsername
     */
    public function setGithubUsername($githubUsername)
    {
        $this->githubUsername = $githubUsername;
    }

    /**
     * @return string
     */
    public function getPipeline()
    {
        return $this->pipeline;
    }

    /**
     * @param string $pipeline
     */
    public function setPipeline($pipeline)
    {
        $this->pipeline = $pipeline;
    }
}
