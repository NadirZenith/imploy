<?php

namespace AppBundle\Security;

use AppBundle\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class GitSecretUserProvider implements UserProviderInterface
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }


    /**
     * @param $email
     * @return null|\AppBundle\Entity\User
     */
    public function loadUserByEmail($email)
    {
        /** @var \AppBundle\Entity\User $user */
        $user = $this->userRepository->findOneBy(array('email' => $email));

        return $user;
    }

    public function loadUserByUsername($username)
    {
        dd($username);
        return false;
        return new User(
            $username,
            null,
            // the roles for the user - you may choose to determine
            // these dynamically somehow based on the user
            array('ROLE_API')
        );
    }

    public function refreshUser(UserInterface $user)
    {
        // this is used for storing authentication in the session
        // but in this example, the token is sent in each request,
        // so authentication can be stateless. Throwing this exception
        // is proper to make things stateless
        throw new UnsupportedUserException();
    }

    public function supportsClass($class)
    {
        return \AppBundle\Entity\User::class === $class;
    }
}

