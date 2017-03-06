<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Log;
use AppBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Tests\AuthClientTrait;

class AppUserControllerTest extends WebTestCase
{
    use AuthClientTrait;

    public function testAppUserListAction($url = '/config/users/list')
    {
        $client = $this->getClient();

        $crawler = $this->getAuthCrawler($client, $url);

        // 3 users listed
        $this->assertGreaterThan(2, $crawler->filter('tbody > tr')->count());

    }

    public function testAppUserCreateAction($url = '/config/users/create')
    {
        $client = $this->getClient();
        $crawler = $this->getAuthCrawler($client, $url);

        $this->assertContains('Create', $crawler->html());
        // can edit roles
        $this->assertContains('id="user_roles"', $crawler->html());

        // LOCAL USER --------------------
        $crawler = $client->submit($crawler->selectButton('user_save')->form());

        //test validation
        $this->assertContains('This value should not be blank.', $crawler->html());

        $form = $crawler->selectButton('user_save')->form();
        // test user creation
        $values = $form->getPhpValues();
        $values['user']['roles'] = array(User::ROLE_SUPER_ADMIN, 'ROLE_ES');
        $values['user']['username'] = 'test';
        $values['user']['email'] = 'test@domain.com';
        $values['user']['plainPassword']['first'] = 'password';
        $values['user']['plainPassword']['second'] = 'password';

        $crawler = $client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
        $this->assertNotContains('has-error', $crawler->html());

        // redirects to update
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirection());

        // LDAP USER --------------------
        $crawler = $client->request('GET', $url);

        $crawler = $client->submit($crawler->selectButton('user_saveLDAP')->form());
        $this->assertContains('This user is not a valid LDAP user', $crawler->html());

        $form = $crawler->selectButton('user_saveLDAP')->form();
        // test user creation
        $values = $form->getPhpValues();
        $values['user']['roles'] = array(User::ROLE_SUPER_ADMIN, 'ROLE_ES');
        $values['user']['email'] = 'test-ldap@domain.com';
        $values['user']['plainPassword']['first'] = 'password';
        $values['user']['plainPassword']['second'] = 'password';

        $crawler = $client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
        $this->assertNotContains('has-error', $crawler->html());

        // redirects to update
        $this->assertEquals($client->getResponse()->getStatusCode(), 302);
        $this->assertTrue($client->getResponse()->isRedirection());

    }

    public function testAppUserCreateFailsAction($url = '/config/users/create')
    {
        $client = $this->getClient();
        $crawler = $this->getAuthCrawler($client, $url);


        // LOCAL USER --------------------
        //@todo

        // LDAP USER --------------------
        $crawler = $client->request('GET', $url);

        // not valid ldap user constraint
        $form = $crawler->selectButton('user_saveLDAP')->form();
        $form->setValues(array(
            'user[email]' => 'not_lda_p@test.dom'
        ));
        $crawler = $client->submit($form);
        $this->assertContains('This user is not a valid LDAP user', $crawler->html());

        // passwords fields don't match
        $form->setValues(array(
            'user[email]'                 => 'ldap@test.dom',
            'user[plainPassword][first]'  => 'pass',
            'user[plainPassword][second]' => 'pass2'
        ));
        $crawler = $client->submit($form);
        $this->assertNotContains('This user is not a valid LDAP user', $crawler->html());
        $this->assertContains('The password fields must match', $crawler->html());

        // bad email
        $form->setValues(array(
            'user[username]'              => 'username',
            'user[email]'                 => 'ldap@test',
            'user[plainPassword][first]'  => 'pass',
            'user[plainPassword][second]' => 'pass'
        ));
        $crawler = $client->submit($form);
        $this->assertNotContains('This user is not a valid LDAP user', $crawler->html());
        $this->assertNotContains('The password fields must match', $crawler->html());
        $this->assertContains('This value is not a valid email address', $crawler->html());

        // success
        $form->setValues(array(
            'user[username]'              => 'username',
            'user[email]'                 => 'ldap@test.dom',
            'user[plainPassword][first]'  => 'pass',
            'user[plainPassword][second]' => 'pass'
        ));
        $crawler = $client->submit($form);
        $this->assertNotContains('This user is not a valid LDAP user', $crawler->html());
        $this->assertNotContains('The password fields must match', $crawler->html());
        $this->assertNotContains('This value is not a valid email address', $crawler->html());

        $this->assertTrue($client->getResponse()->isRedirection());
        $crawler = $client->followRedirect();
        $this->assertContains('User saved', $crawler->html());

        // error existing user
        $form->setValues(array(
            'user[username]'              => 'username',
            'user[email]'                 => 'ldap@test.dom',
            'user[plainPassword][first]'  => 'pass',
            'user[plainPassword][second]' => 'pass'
        ));
        $crawler = $client->submit($form);
        $this->assertNotContains('This user is not a valid LDAP user', $crawler->html());
        $this->assertNotContains('The password fields must match', $crawler->html());
        $this->assertNotContains('This value is not a valid email address', $crawler->html());
        $this->assertContains('This value is already used', $crawler->html());

        $em = $client->getContainer()->get('doctrine')->getManager();
        $em->remove($this->getUserBy(array('email' => 'ldap@test.dom')));
        $em->flush();
    }


    public function testAppUserReadAction($url = '/config/users/%d/read')
    {
        //@todo
    }


    public function testAppUserUpdateAction($url = '/config/users/%d/update')
    {
        $url1 = sprintf($url, $this->getUserBy(array('email' => 'admin@domain.com'))->getId());

        $client = $this->getClient();
        $crawler = $this->getAuthCrawler($client, $url1);

        $this->assertContains('Update', $crawler->html());

        // LOCAL USER --------------------
        $button = $crawler->selectButton('user_save');
        $form = $button->form();

        $values = $form->getPhpValues();
        $values['user']['username'] = 'test-edit';

        $crawler = $client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $this->assertNotContains('has-error', $crawler->html());

        // redirects to update
        $this->assertEquals($client->getResponse()->getStatusCode(), 302);
        $this->assertTrue($client->getResponse()->isRedirection());
        $crawler = $client->followRedirect();

        $form = $crawler->selectButton('user_save')->form();
        $this->assertEquals($form['user']['username']->getValue(), 'test-edit');

    }

    public function testAppUserDeleteAction($url = '/config/users/%d/delete')
    {

        $client = $this->getClient();
        $url = sprintf($url, $this->getUserBy(array('username' => 'test-edit'))->getId());
        $crawler = $this->getAuthCrawler($client, $url, 302);


        // we test this controller action submitting the form with the user_delete button
        $url = '/config/users/%d/update';
        $url = sprintf($url, $this->getUserBy(array('username' => 'test-edit-ldap'))->getId());

        $crawler = $client->request('GET', $url);

        $form = $crawler->selectButton('user_delete')->form();

        $crawler = $client->submit($form);

        // redirects to list
        $this->assertTrue($client->getResponse()->isRedirection());
        $this->assertEquals($client->getResponse()->getStatusCode(), 302);

        $logs = $client->getContainer()->get('doctrine')->getManager()->getRepository(Log::class)->findAll();
        $this->assertGreaterThan(0, count($logs));

    }

    /**
     * @param $client
     * @param $url
     * @return Crawler
     */
    private function getAuthCrawler($client, $url, $final_status_code = 200)
    {
        $crawler = $client->request('GET', $url);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect('http://localhost/login'));

        $this->logIn($client, $this->getUserBy(array('email' => 'user@domain.com')));
        $crawler = $client->request('GET', $url);
        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        $this->logIn($client, $this->getUserBy(array('email' => 'admin@domain.com')));
        $crawler = $client->request('GET', $url);
        $this->assertEquals($final_status_code, $client->getResponse()->getStatusCode());

        return $crawler;
    }

}
