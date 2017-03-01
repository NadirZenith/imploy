<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class LoadUserData implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('admin');
        $user->setEmail('admin@domain.com');
        $user->setPlainPassword('admin');
        $user->setEnabled(true);
        $user->setRoles(array(User::ROLE_SUPER_ADMIN));
        $user->setLocale('en');
        $manager->persist($user);

        $user = new User();
        $user->setUsername('nz');
        $user->setEmail('2cb.md2@gmail.com');
        $user->setPlainPassword('nz');
        $user->setEnabled(true);
        $user->setRoles(array(User::ROLE_SUPER_ADMIN));
        $user->setLocale('es');
        $manager->persist($user);

        $manager->flush();
    }
}
