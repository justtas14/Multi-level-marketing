<?php


namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\ORM\EntityManager;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class PureAdminTest extends WebTestCase
{
    /**
     * @var ReferenceRepository
     */
    private $fixtures;

    /**
     * @group legacy
     */
    protected function setUp() : void
    {
        parent::setUp();
        $this->fixtures = $this->loadFixtures([
            "App\DataFixtures\ORM\LoadUsers",
            "App\DataFixtures\ORM\LoadEmailTemplates",
            "App\DataFixtures\ORM\LoadBlackListEmails"
        ])->getReferenceRepository();
    }

    /**
     *  Testincg end prelaunch feature whith pure admin with no associate logged in
     *
     *  - Login as pure admin and go to end prelaunch form, set end prelaunch to false and submit. Then login with
     * different not admin user.
     *  - Expected to be redirected in associate page after requests to '/' and '/associate' pages. Also expected
     * redirection then requested to '/landingpage' because landing page is not ended.
     *
     *  - Login as pure admin and go to end prelaunch form, set end prelaunch to true and submit. Admin isn't redirected
     * to landing page. Then login with different not admin user.
     *  - Expected to be redirected in landing page after requests to '/' and '/associate' pages and
     * expected appropriate set landing page content. Also expected not to be redirected then requested
     * to '/landingpage' because prelaunch is ended.
     */
    public function testPureAdminEndPrelaunch()
    {
        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var User $user */
        $user = $this->fixtures->getReference('user23');

        $em->refresh($user);

        $email = $user->getEmail();

        $output = $this->runCommand(
            'app:remove:associate',
            [
                'email' => $email
            ]
        )->getDisplay();

        $this->assertContains('Associate successfully removed', $output);

        $this->loginAs($user, 'main');

        $client = $this->makeClient();

        $crawler = $client->request('GET', '/admin/endprelaunch');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Save')->form();

        $form->get('end_prelaunch')['prelaunchEnded']->setValue(false);
        $form->get('end_prelaunch')['landingContent']->setValue("<h1>Prelaunch has ended!!!</h1>");

        $client->submit($form);

        $client->request('GET', '/logout');

        $client->enableProfiler();

        $client->request(
            'POST',
            '/login',
            ['submit' => true, '_username' => 'associate@example.com', '_password' => '1234']
        );

        $client->request('GET', '/');

        $targetUrl = $client->getResponse()->isRedirect("/associate");

        $this->assertTrue($targetUrl);

        $client->request('GET', '/associate');

        $isRedirection = $client->getResponse()->isRedirection();

        $this->assertFalse($isRedirection);

        $client->request('GET', '/landingpage');

        $targetUrl = $client->getResponse()->isRedirect("/");

        $this->assertTrue($targetUrl);

        $client->request('GET', '/logout');

        $client->request(
            'POST',
            '/login',
            ['submit' => true, '_username' => 'PureAdmin@example.com', '_password' => 'admin']
        );

        $crawler = $client->request('GET', '/admin/endprelaunch');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Save')->form();

        $form->get('end_prelaunch')['prelaunchEnded']->setValue(true);

        $client->submit($form);

        $client->request('GET', '/');

        $targetUrl = $client->getResponse()->isRedirect("/admin");

        $this->assertTrue($targetUrl);

        $client->request('GET', '/logout');

        $client->request(
            'POST',
            '/login',
            ['submit' => true, '_username' => 'associate@example.com', '_password' => '1234']
        );

        $client->request('GET', '/');

        $client->getResponse()->isRedirect("/landingpage");

        $client->followRedirect();

        $this->assertContains(
            "<h1>Prelaunch has ended!!!</h1>",
            $client->getResponse()->getContent()
        );

        $client->request('GET', '/associate');

        $targetUrl = $client->getResponse()->isRedirect("/landingpage");

        $this->assertTrue($targetUrl);

        $client->request('GET', '/landingpage');

        $targetUrl = $client->getResponse()->isRedirection();

        $this->assertFalse($targetUrl);
    }

    /**
     *  Testing getCompanyRoot controller with pure admin with no associate logged in
     * if it returns correct json response
     *
     *  - Request to /admin/api/explorer.
     *  - Expect to get json response about company
     *
     *  - Request to /admin/api/explorer with parameter id of 3
     *  - Expected to get 2 associates which has parent id 3 and appropriate values of json response.
     */
    public function testGetCompanyRootWithPureAdmin()
    {
        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var User $user */
        $user = $this->fixtures->getReference('user23');

        $em->refresh($user);

        $email = $user->getEmail();

        $output = $this->runCommand(
            'app:remove:associate',
            [
                'email' => $email
            ]
        )->getDisplay();

        $this->assertContains('Associate successfully removed', $output);

        $this->loginAs($user, 'main');

        $client = $this->makeClient();

        $client->request('GET', '/admin/api/explorer');

        $jsonResponse = $client->getResponse()->getContent();

        $responseArr = json_decode($jsonResponse, true);

        $this->assertEquals(4, sizeof($responseArr));

        $this->assertEquals('-1', $responseArr['id']);
        $this->assertEquals("Company", $responseArr['title']);
        $this->assertEquals('-2', $responseArr['parentId']);
        $this->assertEquals('1', $responseArr['numberOfChildren']);

        $client->request('GET', '/admin/api/explorer', ['id' => '3']);

        $jsonResponse = $client->getResponse()->getContent();

        $responseArr = json_decode($jsonResponse, true);

        $user6 = $em->getRepository(User::class)->find(6);
        $user7 = $em->getRepository(User::class)->find(7);

        $this->assertEquals(2, sizeof($responseArr));

        $usersArray = [
            [
                'id' => $user6->getId(),
                'title' => $user6->getAssociate()->getFullName(),
                'parentId' => $user6->getAssociate()->getParentId(),
                'numberOfChildren' => '1'
            ],
            [
                'id' => $user7->getId(),
                'title' => $user7->getAssociate()->getFullName(),
                'parentId' => $user7->getAssociate()->getParentId(),
                'numberOfChildren' => '3'
            ]
        ];

        $this->assertContains(
            $usersArray[0],
            $responseArr
        );

        $this->assertContains(
            $usersArray[1],
            $responseArr
        );
    }

    /**
     *  Testing admin main page when logged in as pure admin with no associate
     *
     *  - Request to '/' main page and logged in as pure admin.
     *  - Expected to get redirection status code and then redirected to admin main page. Also expected to get
     * appropriate number of sidebar items in menu.
     */
    public function testPureAdminMainPage()
    {
        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var User $user */
        $user = $this->fixtures->getReference('user23');

        $em->refresh($user);

        $email = $user->getEmail();

        $output = $this->runCommand(
            'app:remove:associate',
            [
                'email' => $email
            ]
        )->getDisplay();

        $this->assertContains('Associate successfully removed', $output);

        $this->loginAs($user, 'main');

        $client = $this->makeClient();

        $client->request('GET', '/');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $crawler = $client->followRedirect();

        $this->assertEquals('/admin', $client->getRequest()->getRequestUri());

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals(
            9,
            $crawler->filter('div.sidebar-item')->count()
        );
    }


    /**
     *  Testing /associate/info api whether it returns empty response when logged in as admin without associate.
     */
    public function testGetBrokenAssociateWithPureAdmin()
    {
        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var User $user */
        $user = $this->fixtures->getReference('user23');

        $em->refresh($user);
        $this->loginAs($user, 'main');

        $client = $this->makeClient();

        $client->request('GET', '/associate/info');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals(
            '',
            $client->getResponse()->getContent()
        );
    }

    /**
     *  Testing get associate api when logged in as admin without associate
     *
     *  - Request to /associate/info/2 api.
     *  - Expect to go to information page about associate 2.
     *
     *  - Request to /associate/info/3 api.
     *  - Expect to go to information page about associate 3.
     *
     *  - Request to /associate/info/-1 api.
     *  - Expect to go to information page about company because associate with id -1 was not found.
     *
     *  - Request to /associate/info/700 api.
     *  - Expect to go to information page about company because associate with id 700 was not found.
     */
    public function testGetAssociateWithPureAdmin()
    {
        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var User $user */
        $user = $this->fixtures->getReference('user23');

        $em->refresh($user);
        $this->loginAs($user, 'main');

        $client = $this->makeClient();

        $crawler = $client->request('GET', '/associate/info/2');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $user = $this->fixtures->getReference('user2');

        $this->assertContains(
            $user->getAssociate()->getFullName(),
            $crawler->filter('div > div')->eq(1)->filter('p')->eq(0)->html()
        );

        $this->assertContains(
            $user->getAssociate()->getEmail(),
            $crawler->filter('div > div')->eq(1)->filter('p')->eq(1)->html()
        );

        $crawler = $client->request('GET', '/associate/info/3');

        $user = $this->fixtures->getReference('user3');

        $this->assertContains(
            $user->getAssociate()->getFullName(),
            $crawler->filter('div > div')->eq(1)->filter('p')->eq(0)->html()
        );

        $this->assertContains(
            $user->getAssociate()->getEmail(),
            $crawler->filter('div > div')->eq(1)->filter('p')->eq(1)->html()
        );

        $client->request('GET', '/associate/info/-1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertContains(
            '<b>Title</b>: Company',
            $client->getResponse()->getContent()
        );

        $client->request('GET', '/associate/info/700');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertContains(
            '<b>Title</b>: Company',
            $client->getResponse()->getContent()
        );
    }
}
