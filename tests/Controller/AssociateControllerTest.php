<?php


namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\ORM\EntityManager;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class AssociateControllerTest extends WebTestCase
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
            "App\DataFixtures\ORM\LoadEmailTemplates"
        ])->getReferenceRepository();
    }

    /**
     *  Testing user logged in update associate form if it updates correctly
     *
     *  - Input all assumed correct update values in form.
     *  - Expected for user to be updated all appropriate inputed values without any error.
     *  - Input all correct values except email which is already taken.
     *  - Expected flash message error to appear which states that email is already taken.
     *  - Input all correct values except old password which is not correct.
     *  - Expected flash message error to appear which states that old password is not correct.
     *  - Input all correct values except repeated new password is left blank.
     *  - After update expected that user password is not updated to blank but left with old password.
     */
    public function testUpdateAssociate()
    {
        $container = $this->getContainer();

        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var User $user */
        $user = $this->fixtures->getReference('user2');

        $em->refresh($user);
        $this->loginAs($user, 'main');

        $client = $this->makeClient();

        $crawler = $client->request('GET', '/associate/profile');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Save')->form();

        $form->get('user_update')['email']->setValue($user->getEmail());
        $form->get('user_update')['oldPassword']->setValue('1234');
        $form->get('user_update')['newPassword']['first']->setValue('12345');
        $form->get('user_update')['newPassword']['second']->setValue('12345');
        $form->get('user_update')['associate']['email']->setValue($user->getEmail());
        $form->get('user_update')['associate']['fullName']->setValue('Justas');
        $form->get('user_update')['associate']['country']->setValue('LT');
        $form->get('user_update')['associate']['address']->setValue('blaha');
        $form->get('user_update')['associate']['city']->setValue('kretinga');
        $form->get('user_update')['associate']['postcode']->setValue('12345');
        $form->get('user_update')['associate']['mobilePhone']['number']->setValue('7393334589');
        $form->get('user_update')['associate']['homePhone']->setValue('23543');
        $form->get('user_update')['associate']['agreedToEmailUpdates']->setValue(1);
        $form->get('user_update')['associate']['agreedToTextMessageUpdates']->setValue(1);
        $form->get('user_update')['associate']['agreedToTermsOfService']->setValue(1);
        $form->get('user_update')['associate']['profilePicture']->setValue(null);

        $client->submit($form);

        $em->refresh($user);

        $this->assertEquals('associate@example.com', $user->getEmail());

        $encoder = $container->get('security.user_password_encoder.generic');
        $this->assertTrue($encoder->isPasswordValid($user, '12345'));

        $this->assertEquals('associate@example.com', $user->getAssociate()->getEmail());
        $this->assertEquals('Justas', $user->getAssociate()->getFullName());
        $this->assertEquals('LT', $user->getAssociate()->getCountry());
        $this->assertEquals('blaha', $user->getAssociate()->getAddress());
        $this->assertEquals('kretinga', $user->getAssociate()->getCity());
        $this->assertEquals('12345', $user->getAssociate()->getPostcode());
        $this->assertEquals('+447393334589', $user->getAssociate()->getMobilePhone());
        $this->assertEquals('23543', $user->getAssociate()->getHomePhone());
        $this->assertEquals(true, $user->getAssociate()->isAgreedToEmailUpdates());
        $this->assertEquals(true, $user->getAssociate()->isAgreedToTextMessageUpdates());
        $this->assertEquals(null, $user->getAssociate()->getProfilePicture());


        $crawler = $client->request('GET', '/associate/profile');

        $form = $crawler->selectButton('Save')->form();

        $form->get('user_update')['email']->setValue("admin@plumtreesystems.com");
        $form->get('user_update')['oldPassword']->setValue('12345');
        $form->get('user_update')['newPassword']['first']->setValue('justtas');
        $form->get('user_update')['newPassword']['second']->setValue('justtas');
        $form->get('user_update')['associate']['email']->setValue("admin@plumtreesystems.com");
        $form->get('user_update')['associate']['fullName']->setValue('Justas');
        $form->get('user_update')['associate']['country']->setValue('LT');
        $form->get('user_update')['associate']['address']->setValue('blaha');
        $form->get('user_update')['associate']['city']->setValue('kretinga');
        $form->get('user_update')['associate']['postcode']->setValue('12345');
        $form->get('user_update')['associate']['mobilePhone']['number']->setValue('7393334589');
        $form->get('user_update')['associate']['homePhone']->setValue('23543');
        $form->get('user_update')['associate']['agreedToEmailUpdates']->setValue(1);
        $form->get('user_update')['associate']['agreedToTextMessageUpdates']->setValue(1);
        $form->get('user_update')['associate']['agreedToTermsOfService']->setValue(1);
        $form->get('user_update')['associate']['profilePicture']->setValue(null);

        $crawler = $client->submit($form);

        $em->refresh($user);

        $this->assertContains(
            'This email already exist',
            $crawler->filter('#flash-messages')->html()
        );

        $crawler = $client->request('GET', '/associate/profile');

        $form = $crawler->selectButton('Save')->form();

        $form->get('user_update')['email']->setValue($user->getEmail());
        $form->get('user_update')['oldPassword']->setValue('somepasw');
        $form->get('user_update')['newPassword']['first']->setValue('justtas');
        $form->get('user_update')['newPassword']['second']->setValue('justtas');
        $form->get('user_update')['associate']['email']->setValue($user->getEmail());
        $form->get('user_update')['associate']['fullName']->setValue('Justas');
        $form->get('user_update')['associate']['country']->setValue('LT');
        $form->get('user_update')['associate']['address']->setValue('blaha');
        $form->get('user_update')['associate']['city']->setValue('kretinga');
        $form->get('user_update')['associate']['postcode']->setValue('12345');
        $form->get('user_update')['associate']['mobilePhone']['number']->setValue('7393334589');
        $form->get('user_update')['associate']['homePhone']->setValue('23543');
        $form->get('user_update')['associate']['agreedToEmailUpdates']->setValue(1);
        $form->get('user_update')['associate']['agreedToTextMessageUpdates']->setValue(1);
        $form->get('user_update')['associate']['agreedToTermsOfService']->setValue(1);
        $form->get('user_update')['associate']['profilePicture']->setValue(null);

        $crawler = $client->submit($form);

        $em->refresh($user);

        $this->assertContains(
            'Old password is not correct',
            $crawler->filter('#flash-messages')->html()
        );

        $crawler = $client->request('GET', '/associate/profile');

        $form = $crawler->selectButton('Save')->form();

        $form->get('user_update')['email']->setValue($user->getEmail());
        $form->get('user_update')['oldPassword']->setValue('12345');
        $form->get('user_update')['newPassword']['first']->setValue('');
        $form->get('user_update')['newPassword']['second']->setValue('');
        $form->get('user_update')['associate']['email']->setValue($user->getEmail());
        $form->get('user_update')['associate']['fullName']->setValue('Justas');
        $form->get('user_update')['associate']['country']->setValue('LT');
        $form->get('user_update')['associate']['address']->setValue('blaha');
        $form->get('user_update')['associate']['city']->setValue('kretinga');
        $form->get('user_update')['associate']['postcode']->setValue('12345');
        $form->get('user_update')['associate']['mobilePhone']['number']->setValue('7393334589');
        $form->get('user_update')['associate']['homePhone']->setValue('23543');
        $form->get('user_update')['associate']['agreedToEmailUpdates']->setValue(1);
        $form->get('user_update')['associate']['agreedToTextMessageUpdates']->setValue(1);
        $form->get('user_update')['associate']['agreedToTermsOfService']->setValue(1);
        $form->get('user_update')['associate']['profilePicture']->setValue(null);

        $client->submit($form);

        $em->refresh($user);

        $encoder = $container->get('security.user_password_encoder.generic');
        $this->assertTrue($encoder->isPasswordValid($user, '12345'));
    }

    /**
     *  Testing admin logged in update associate form if it updates correctly
     *
     *  - Go to /associate/profile api, expected that old phone is not correct error.
     * Then input all assumed correct update values in form.
     *  - Expected for user to be updated all appropriate inputed values without any error.
     *
     *  - Input all correct values except email which is already taken.
     *  - Expected flash message error to appear which states that email is already taken.
     *
     *  - Input all correct values except old password which is not correct.
     *  - Expected flash message error to appear which states that old password is not correct.
     *
     *  - Input all correct values except repeated new password is left blank.
     *  - After update expected that user password is not updated to blank but left with old password.
     *
     *  - Input all correct values except now testing if profile picture remains the same if input is null
     *  - After update expected that profile picture is not null.
     */
    public function testAdminUpdateAssociate()
    {
        $container = $this->getContainer();

        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var User $user */
        $user = $this->fixtures->getReference('user1');

        $em->refresh($user);
        $this->loginAs($user, 'main');

        $client = $this->makeClient();

        $crawler = $client->request('GET', '/associate/profile');

        $this->assertContains(
            'Prior mobile is invalid',
            $crawler->filter('div.error__block')->html()
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Save')->form();

        $path = $client->getContainer()->getParameter('kernel.project_dir').'/var/test_files';

        $fileInput = $form->get('user_update')['associate']['profilePicture'];
        $fileInput->upload($path.'/test.png');

        $files = $form->getPhpFiles();
        $files['user_update']['associate']['profilePicture']['type'] = 'image/jpeg';
        $csrf_protection = $form['user_update']['_token'];

        $client->request(
            'POST',
            '/associate/profile',
            [
                'submit' => true,
                'user_update' => [
                    '_token' => $csrf_protection->getValue(),
                    'email' => $user->getEmail(),
                    'oldPassword' => '123456789',
                    'newPassword' => [
                        'first' => '12345',
                        'second' => '12345'
                    ],
                    'associate' => [
                        'fullName' => 'Justas',
                        'country' => 'LT',
                        'address' => 'blaha',
                        'city' => 'kretinga',
                        'postcode' => '12345',
                        'mobilePhone' => ['country' => 'GB', 'number' => '7393334589'],
                        'homePhone' => '23543',
                        'agreedToEmailUpdates' => '1',
                        'agreedToTextMessageUpdates' => '1',
                        'agreedToTermsOfService' => '1'
                    ]
                ]
            ],
            $files
        );

        $em->refresh($user);

        $this->assertEquals('admin@plumtreesystems.com', $user->getEmail());

        $encoder = $container->get('security.user_password_encoder.generic');
        $this->assertTrue($encoder->isPasswordValid($user, '12345'));

        $this->assertEquals('Justas', $user->getAssociate()->getFullName());
        $this->assertEquals('LT', $user->getAssociate()->getCountry());
        $this->assertEquals('blaha', $user->getAssociate()->getAddress());
        $this->assertEquals('kretinga', $user->getAssociate()->getCity());
        $this->assertEquals('12345', $user->getAssociate()->getPostcode());
        $this->assertEquals('+447393334589', $user->getAssociate()->getMobilePhone());
        $this->assertEquals('23543', $user->getAssociate()->getHomePhone());
        $this->assertEquals(true, $user->getAssociate()->isAgreedToEmailUpdates());
        $this->assertEquals(true, $user->getAssociate()->isAgreedToTextMessageUpdates());
        $this->assertNotNull($user->getAssociate()->getProfilePicture());


        $crawler = $client->request('GET', '/associate/profile');

        $form = $crawler->selectButton('Save')->form();

        $form->get('user_update')['email']->setValue("BaileyBrookes@dayrep.com");
        $form->get('user_update')['oldPassword']->setValue('12345');
        $form->get('user_update')['newPassword']['first']->setValue('123456789');
        $form->get('user_update')['newPassword']['second']->setValue('123456789');
        $form->get('user_update')['associate']['fullName']->setValue('Justas');
        $form->get('user_update')['associate']['country']->setValue('LT');
        $form->get('user_update')['associate']['address']->setValue('blaha');
        $form->get('user_update')['associate']['city']->setValue('kretinga');
        $form->get('user_update')['associate']['postcode']->setValue('12345');
        $form->get('user_update')['associate']['mobilePhone']['number']->setValue('7393334589');
        $form->get('user_update')['associate']['homePhone']->setValue('23543');
        $form->get('user_update')['associate']['agreedToEmailUpdates']->setValue(1);
        $form->get('user_update')['associate']['agreedToTextMessageUpdates']->setValue(1);
        $form->get('user_update')['associate']['agreedToTermsOfService']->setValue(1);
        $form->get('user_update')['associate']['profilePicture']->setValue(null);

        $crawler = $client->submit($form);

        $em->refresh($user);

        $this->assertContains(
            'This email already exist',
            $crawler->filter('#flash-messages')->html()
        );

        $crawler = $client->request('GET', '/associate/profile');

        $form = $crawler->selectButton('Save')->form();

        $form->get('user_update')['email']->setValue($user->getEmail());
        $form->get('user_update')['oldPassword']->setValue('somepasw');
        $form->get('user_update')['newPassword']['first']->setValue('justtas');
        $form->get('user_update')['newPassword']['second']->setValue('justtas');
        $form->get('user_update')['associate']['fullName']->setValue('Justas');
        $form->get('user_update')['associate']['country']->setValue('LT');
        $form->get('user_update')['associate']['address']->setValue('blaha');
        $form->get('user_update')['associate']['city']->setValue('kretinga');
        $form->get('user_update')['associate']['postcode']->setValue('12345');
        $form->get('user_update')['associate']['mobilePhone']['number']->setValue('7393334589');
        $form->get('user_update')['associate']['homePhone']->setValue('23543');
        $form->get('user_update')['associate']['agreedToEmailUpdates']->setValue(1);
        $form->get('user_update')['associate']['agreedToTextMessageUpdates']->setValue(1);
        $form->get('user_update')['associate']['agreedToTermsOfService']->setValue(1);
        $form->get('user_update')['associate']['profilePicture']->setValue(null);

        $crawler = $client->submit($form);

        $em->refresh($user);

        $this->assertContains(
            'Old password is not correct',
            $crawler->filter('#flash-messages')->html()
        );

        $crawler = $client->request('GET', '/associate/profile');

        $form = $crawler->selectButton('Save')->form();

        $client->request(
            'POST',
            '/associate/profile',
            [
                'submit' => true,
                'user_update' => [
                    '_token' => $csrf_protection->getValue(),
                    'email' => $user->getEmail(),
                    'oldPassword' => '12345',
                    'newPassword' => [
                        'first' => '',
                        'second' => ''
                    ],
                    'associate' => [
                        'fullName' => 'Justas',
                        'country' => 'LT',
                        'address' => 'blaha',
                        'city' => 'kretinga',
                        'postcode' => '12345',
                        'mobilePhone' => ['country' => 'GB', 'number' => '7393334589'],
                        'homePhone' => '23543',
                        'agreedToEmailUpdates' => '1',
                        'agreedToTextMessageUpdates' => '1',
                        'agreedToTermsOfService' => '1'
                    ]
                ]
            ],
            $files
        );

        $client->submit($form);

        $em->refresh($user);

        $encoder = $container->get('security.user_password_encoder.generic');
        $this->assertTrue($encoder->isPasswordValid($user, '12345'));


        $crawler = $client->request('GET', '/associate/profile');

        $form = $crawler->selectButton('Save')->form();

        $form->get('user_update')['email']->setValue($user->getEmail());
        $form->get('user_update')['oldPassword']->setValue('12345');
        $form->get('user_update')['newPassword']['first']->setValue('');
        $form->get('user_update')['newPassword']['second']->setValue('');
        $form->get('user_update')['associate']['fullName']->setValue('Justas');
        $form->get('user_update')['associate']['country']->setValue('LT');
        $form->get('user_update')['associate']['address']->setValue('blaha');
        $form->get('user_update')['associate']['city']->setValue('kretinga');
        $form->get('user_update')['associate']['postcode']->setValue('12345');
        $form->get('user_update')['associate']['mobilePhone']['number']->setValue('7393334589');
        $form->get('user_update')['associate']['homePhone']->setValue('23543');
        $form->get('user_update')['associate']['agreedToEmailUpdates']->setValue(1);
        $form->get('user_update')['associate']['agreedToTextMessageUpdates']->setValue(1);
        $form->get('user_update')['associate']['agreedToTermsOfService']->setValue(1);
        $form->get('user_update')['associate']['profilePicture']->setValue(null);

        $client->submit($form);
        $em->refresh($user);

        $this->assertNotNull($user->getAssociate()->getProfilePicture());

        $gaufretteFilteManager = $container->get('pts_file.manager');

        $em = $container->get('doctrine.orm.default_entity_manager');

        $fileObj = $em->getRepository(\App\Entity\File::class);

        $allFiles = $fileObj->findAll();

        foreach ($allFiles as $file) {
            $gaufretteFilteManager->remove($file);
        }
    }

    /**
     *  Testing directDownline controller if it returns correct json response
     *
     *  - Request to /associate/downline without params.
     *  - Expect to get json response about currently logged in associate.
     *
     *  - Request to /admin/api/explorer with parameter id of 2.
     *  - Expected to get 2 associates which has parent id 2 and appropriate values of json response.
     *
     *  - Request to /admin/api/explorer with parameter id of 2 and logged in as associate with id 6.
     *  - Expected to get 403 status code because associate with id 6 doesnt have associate of id 2 in it's downline.
     */
    public function testDirectDownline()
    {
        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var User $user */
        $user = $this->fixtures->getReference('user2');

        $em->refresh($user);
        $this->loginAs($user, 'main');

        $client = $this->makeClient();

        $client->request('GET', '/associate/downline');

        $jsonResponse = $client->getResponse()->getContent();

        $responseArr = json_decode($jsonResponse, true);

        $this->assertEquals(4, sizeof($responseArr));

        $this->assertEquals($user->getId(), $responseArr['id']);
        $this->assertEquals($user->getAssociate()->getFullName(), $responseArr['title']);
        $this->assertEquals($user->getAssociate()->getParentId(), $responseArr['parentId']);
        $this->assertEquals('2', $responseArr['numberOfChildren']);

        $client->request('GET', '/associate/downline', ['id' => 2]);

        $jsonResponse = $client->getResponse()->getContent();

        $responseArr = json_decode($jsonResponse, true);

        $user4 = $em->getRepository(User::class)->find(4);
        $user5 = $em->getRepository(User::class)->find(5);

        $this->assertEquals(2, sizeof($responseArr));

        $usersArray = [
            [
                'id' => $user4->getId(),
                'title' => $user4->getAssociate()->getFullName(),
                'parentId' => $user4->getAssociate()->getParentId(),
                'numberOfChildren' => '0'
            ],
            [
                'id' => $user5->getId(),
                'title' => $user5->getAssociate()->getFullName(),
                'parentId' => $user5->getAssociate()->getParentId(),
                'numberOfChildren' => '2'
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

        $client->request('GET', '/logout');

        $client->request(
            'POST',
            '/login',
            ['submit' => true, '_username' => 'NatashaHutchinson@rhyta.com', '_password' => '1234']
        );

        $client->request('GET', '/associate/downline', ['id' => 2]);
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    /**
     *  Testing associate main page
     *
     *  - Request to '/' main page and logged in as associate.
     *  - Expected to get redirection status code and then redirected to associate main page. Also expected to get
     * appropriate number of levelBarLIstItem in associate main page.
     *
     *  - Request to '/associate' main page and logged in as admin.
     *  - Expected to go to /associate main page and also to get appropriate number of levelBarListItem in associate
     * main page.
     */
    public function testAssociateMainPage()
    {
        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var User $user */
        $user = $this->fixtures->getReference('user2');

        $em->refresh($user);
        $this->loginAs($user, 'main');

        $client = $this->makeClient();

        $client->request('GET', '/');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $crawler = $client->followRedirect();

        $this->assertEquals('/associate', $client->getRequest()->getRequestUri());

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals(
            4,
            $crawler->filter('li.associate-levelBarListItem')->count()
        );

        $client->request('GET', '/logout');

        $client->request(
            'POST',
            '/login',
            ['submit' => true, '_username' => 'admin@plumtreesystems.com', '_password' => '123456789']
        );

        $crawler = $client->request('GET', '/associate');

        $this->assertEquals('/associate', $client->getRequest()->getRequestUri());

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals(
            5,
            $crawler->filter('li.associate-levelBarListItem')->count()
        );
    }

    /**
     *  Testing whether associate can go to /associate/viewer api.
     */
    public function testTeamViewer()
    {
        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var User $user */
        $user = $this->fixtures->getReference('user2');

        $em->refresh($user);
        $this->loginAs($user, 'main');

        $client = $this->makeClient();

        $crawler = $client->request('GET', '/associate/viewer');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals(
            1,
            $crawler->filter('span.card-title')->count()
        );
    }

    /**
     *  Testing /associate/info api whether it returns empty response.
     */
    public function testGetBrokenAssociate()
    {
        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var User $user */
        $user = $this->fixtures->getReference('user2');

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
     *  Testing get associtate api
     *
     *  - Request to /associate/info/2 api when logged in as user 2 associate.
     *  - Expect to go to information page about associate 2.
     *
     *  - Request to /associate/info/3 api when logged in as user 2 associate.
     *  - Expect to get error status code becuase user 3 is not in associate with id 2 downline.
     *
     *  - Request to /associate/info/1 api when logged in as user 2 associate.
     *  - Expect to get error status code becuase user 1 is not in associate with id 2 downline.
     *
     *  - Request to /associate/info/1 api when logged in as admin.
     *  - Expect to go to information page about associate 1.
     *
     *  - Request to /associate/info/3 api when logged in as admin.
     *  - Expect to go to information page about associate 3.
     *
     *  - Request to /associate/info/-1 api when logged in as admin.
     *  - Expect to go to information page about company becuase associate with id -1 was not found.
     *
     *  - Request to /associate/info/700 api when logged in as admin.
     *  - Expect to go to information page about company becuase associate with id 700 was not found.
     */
    public function testGetAssociate()
    {
        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var User $user */
        $user = $this->fixtures->getReference('user2');

        $em->refresh($user);
        $this->loginAs($user, 'main');

        $client = $this->makeClient();

        $crawler = $client->request('GET', '/associate/info/2');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertContains(
            $user->getAssociate()->getFullName(),
            $crawler->filter('div > div')->eq(1)->filter('p')->eq(0)->html()
        );

        $this->assertContains(
            $user->getAssociate()->getEmail(),
            $crawler->filter('div > div')->eq(1)->filter('p')->eq(1)->html()
        );

        $client->request('GET', '/associate/info/1');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        $client->request('GET', '/associate/info/3');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());

        $client->request('GET', '/logout');

        $client->request(
            'POST',
            '/login',
            ['submit' => true, '_username' => 'admin@plumtreesystems.com', '_password' => '123456789']
        );

        $user = $this->fixtures->getReference('user1');

        $crawler = $client->request('GET', '/associate/info/1');

        $this->assertContains(
            $user->getAssociate()->getFullName(),
            $crawler->filter('div > div')->eq(1)->filter('p')->eq(0)->html()
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

    /**
     *  Testing recent paginations pagination and status codes if user inputs various pages.
     *
     *  - Request to /associate/invite page with param of page equal 0.
     *  - Expected to get 200 status code.
     *
     *  - Request to /associate/invite page with param of page equal 'fasfa'.
     *  - Expected to get 404 status code and page not found message.
     *
     *  - Request to /associate/invite page with param of page equal 400.
     *  - Expected to get 404 status code and page 400 not found message.
     */
    public function testRecentInvitationsPagination()
    {
        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var User $user */
        $user = $this->fixtures->getReference('user21');

        $em->refresh($user);
        $this->loginAs($user, 'main');

        $client = $this->makeClient();

        $client->request('GET', '/associate/invite', ['page' => 1]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', '/associate/invite', ['page' => 'fasfsa']);

        $this->assertEquals(404, $client->getResponse()->getStatusCode());

        $this->assertContains('Page not found', $client->getResponse()->getContent());

        $client->request('GET', '/associate/invite', ['page' => 400]);

        $this->assertContains('Page 400 doesnt exist', $client->getResponse()->getContent());
    }
}
