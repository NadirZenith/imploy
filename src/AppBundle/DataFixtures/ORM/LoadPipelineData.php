<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Pipeline;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


class LoadPipelineData implements FixtureInterface, ContainerAwareInterface
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
        $pipeline = new Pipeline();
        $pipeline->setName('nzlab pre');
        $pipeline->setUrl('https://github.com/NadirZenith/nzlab.es');
        $pipeline->setSecurityToken('secret');
        $pipeline->setEnvironment('dev');
        $manager->persist($pipeline);

        $pipeline = new Pipeline();
        $pipeline->setName('test master');
        $pipeline->setUrl('https://gitlab.com/NadirZenith/test');
        $pipeline->setSecurityToken('secret');
        $pipeline->setEnvironment('master');
        $manager->persist($pipeline);

        $manager->flush();
    }
}
