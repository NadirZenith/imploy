<?php

namespace Tests;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

trait AuthClientTrait
{
    private $client = null;

    public function setUp()
    {
        $this->client = parent::createClient();
    }

    /**
     * @return Client
     */
    private function getClient()
    {
        return $this->client;
    }

    /**
     * @param array $filter
     * @return mixed
     */
    private function getUserBy(array $filter = array(), $user_class = User::class)
    {
        return $this->getClient()->getContainer()->get('doctrine')->getRepository($user_class)->findOneBy($filter);
    }

    private function logIn($client, User $user, $firewall = 'main')
    {
        // the firewall context (defaults to the firewall name)
        $session = $client->getContainer()->get('session');
        $token = new UsernamePasswordToken($user, null, $firewall, $user->getRoles());
        //$client->getContainer()->get('security.token_storage')->setToken($token);

        $session->set('_security_' . $firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);
    }

}
