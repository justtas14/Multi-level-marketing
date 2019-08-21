<?php


namespace App\Tests\Controller;

use App\Service\CreateAdmin;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class AdminControllerFindAssociatesTest extends WebTestCase
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
        $this->fixtures = $this->loadFixtures([])->getReferenceRepository();
    }

    /**
     *  Testing findAssociates admin controller json response whether it returns correct associates
     *
     *  - Request /admin/api/associates endpoint with no parameters.
     *  - Expected to get 10 (associate limit) first associates with params fullName, mobilePhone and email
     * and appropriate values in json Format. Also expected to get 3 max number of pages because it is calculated by
     * formula: ceil (all associates / associate limit) => ceil (22 / 10) => 3
     *
     *  - Reqiest /admin/api/associates endpoint with page 2 param.
     *  - Expected to get another 10 associates from database with params fullName, mobilePhone and email
     * and appropriate values in json Format. Also expected to get 3 max number of pages because it is calculated by
     * formula: ceil (all associates / associate limit) => ceil (22 / 10) => 3
     *
     *  - Reqiest /admin/api/associates endpoint with page 3 param which is max page of all associates.
     *  - Expected to get left 2 associates from database with params fullName, mobilePhone and email.
     * and appropriate values in json Format. Also expected to get 3 max number of pages because it is calculated by
     * formula: ceil (all associates / associate limit) => ceil (22 / 10) => 3
     *
     *  - Reqiest /admin/api/associates endpoint with page 4 param which is incorrect parameter because page 3 is max
     * of associates in pagination
     *  - Expected 404 not found error and exception to be thrown with appropriate error message and error page.
     *
     *  - Reqiest /admin/api/associates endpoint with page -4 param which is incorrect parameter.
     *  - Expected 404 not found error and exception to be thrown with appropriate error message and error page.
     *
     *  - Reqiest /admin/api/associates endpoint with page '-saf' param which is incorrect parameter.
     *  - Expected 404 not found error and exception to be thrown with appropriate error message and error page.
     *
     *  - Reqiest /admin/api/associates endpoint with nameField with value S param.
     *  - Expected to get 10 first associates (associate limit) from database which has S letter in their names.
     * Expected appropriate associate param fullName, mobilePhone and email values in json Format.
     * Also expected to get 2 max number of pages because it is calculated by formula:
     * ceil (all associates / associate limit) => ceil (14 / 10) => 2.
     *
     *  - Reqiest /admin/api/associates endpoint with nameField with value S and page with value 2 in param.
     *  - Expected to get 4 left associates from database which have S letter in their names.
     * Expected appropriate associate param fullName, mobilePhone and email values in json Format.
     * Also expected to get 2 max number of pages because it is calculated by formula:
     * ceil (all associates / associate limit) => ceil (14 / 10) => 2.
     *
     *  - Reqiest /admin/api/associates endpoint with nameField with value Is in params.
     *  - Expected to get 4 associates from database which have Is word in their names.
     * Expected appropriate associate param fullName, mobilePhone and email values in json Format.
     * Also expected to get 1 max number of pages because it is calculated by formula:
     * ceil (all associates / associate limit) => ceil (4 / 10) => 1.
     *
     *  - Reqiest /admin/api/associates endpoint with nameField with value Is and emailField with value AU in params.
     *  - Expected to get 2 associates from database which have Is word in their names AND AU word in theirs emails
     * Expected appropriate associate param fullName, mobilePhone and email values in json Format.
     * Also expected to get 1 max number of pages because it is calculated by formula:
     * ceil (all associates / associate limit) => ceil (2 / 10) => 1.
     *
     *  - Reqiest /admin/api/associates endpoint with nameField with value Is, emailField with value AU and
     * telephoneField with value 2.
     *  - Expected to get 1 associate from database which has Is word in it's names, AU word in it's emails and
     * 2 number it's number;
     * Expected appropriate associate param fullName, mobilePhone and email values in json Format.
     * Also expected to get 1 max number of pages because it is calculated by formula:
     * ceil (all associates / associate limit) => ceil (1 / 10) => 1.
     *
     *  - Reqiest /admin/api/associates endpoint with nameField with value Isff, emailField with value AU and
     * telephoneField with value 2.
     *  - Expected to get 0 associate from database which has Is word in it's names, AU word in it's emails and
     * 2 number it's number;
     * Also expected to get 1 max number of pages.
     */
    public function testFindAssociates()
    {
        self::bootKernel();

        $container = self::$container;

        $em = $container->get('doctrine.orm.entity_manager');

        $createAdmin = $container->get('App\Service\CreateAdmin');

        $user1 = $createAdmin->createAdmin(
            'justtas14@gmail.com',
            'justtas14',
            'Justas',
            '12455412'
        );
        $user2 = $createAdmin->createAdmin(
            'vanagas@gmail.com',
            'vanagas',
            'Vanagas',
            '44864'
        );

        $user3 = $createAdmin->createAdmin(
            'paukstis@gmail.com',
            'paukstis',
            'Paukstis',
            '254345'
        );
        $user4 = $createAdmin->createAdmin(
            'draustinis@gmail.com',
            'draustinis',
            'Draustinis',
            '14546'
        );
        $user5 = $createAdmin->createAdmin(
            'dangus@gmail.com',
            'dangus',
            'Dangus',
            '45454'
        );
        $user6 = $createAdmin->createAdmin(
            'rankena@gmail.com',
            'rankena',
            'Rankena',
            '865483621'
        );
        $user7 = $createAdmin->createAdmin(
            'lempa@gmail.com',
            'lempa',
            'Lempa',
            '122545'
        );
        $user8 = $createAdmin->createAdmin(
            'uzuolaida@gmail.com',
            'uzuolaida',
            'Uzuolaida',
            '24534543'
        );
        $user9 = $createAdmin->createAdmin(
            'skritulys@gmail.com',
            'skritulys',
            'Skritulys',
            '152543'
        );
        $user10 = $createAdmin->createAdmin(
            'puodas@gmail.com',
            'puodas',
            'Puodas',
            '21524152'
        );
        $user11 = $createAdmin->createAdmin(
            'draugas@gmail.com',
            'draugas',
            'Draugas',
            '214152'
        );
        $user12 = $createAdmin->createAdmin(
            'priesas@gmail.com',
            'priesas',
            'Priesas',
            '2152'
        );
        $user13 = $createAdmin->createAdmin(
            'kompas@gmail.com',
            'kompas',
            'Kompas',
            '1441'
        );
        $user14 = $createAdmin->createAdmin(
            'wifi@gmail.com',
            'wifi',
            'Wifi',
            '54578'
        );
        $user15 = $createAdmin->createAdmin(
            'penktadienis@gmail.com',
            'penktadienis',
            'Penktadienis',
            '12453'
        );
        $user16 = $createAdmin->createAdmin(
            'veidrodis@gmail.com',
            'veidrodis',
            'Veidrodis',
            '144114'
        );
        $user17 = $createAdmin->createAdmin(
            'ekecia@gmail.com',
            'ekecia',
            'Ekecia',
            '25412423'
        );
        $user18 = $createAdmin->createAdmin(
            'baterija@gmail.com',
            'baterija',
            'Baterija',
            '254123'
        );
        $user19 = $createAdmin->createAdmin(
            'suva@gmail.com',
            'suva',
            'Suva',
            '14214'
        );
        $user20 = $createAdmin->createAdmin(
            'kate@gmail.com',
            'kate',
            'kate',
            '1447214'
        );
        $user21 = $createAdmin->createAdmin(
            'diena@gmail.com',
            'diena',
            'diena',
            '14414'
        );
        $user22 = $createAdmin->createAdmin(
            'horizontas@gmail.com',
            'horizontas',
            'horizontas',
            '144147'
        );

        $this->loginAs($user1, 'main');

        $client = $this->makeClient();

        $client->request('GET', '/admin/api/associates');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $jsonResponse = $client->getResponse()->getContent();

        $responseArr = json_decode($jsonResponse, true);

        $this->assertEquals(10, sizeof($responseArr['associates']));

        $this->assertEquals($user1->getAssociate()->getId(), $responseArr['associates']['0']['id']);
        $this->assertEquals($user1->getAssociate()->getLevel(), $responseArr['associates']['0']['level']);
        $this->assertEquals($user1->getAssociate()->getFullName(), $responseArr['associates']['0']['fullName']);
        $this->assertEquals(
            $user1->getAssociate()->getMobilePhone(),
            $responseArr['associates']['0']['mobilePhone']
        );
        $this->assertEquals($user1->getAssociate()->getEmail(), $responseArr['associates']['0']['email']);
        $this->assertEquals(
            $user1->getAssociate()->getJoinDate()->format("Y-m-d"),
            $responseArr['associates']['0']['joinDate']
        );

        $this->assertEquals($user10->getAssociate()->getId(), $responseArr['associates']['9']['id']);
        $this->assertEquals($user10->getAssociate()->getLevel(), $responseArr['associates']['9']['level']);
        $this->assertEquals($user10->getAssociate()->getFullName(), $responseArr['associates']['9']['fullName']);
        $this->assertEquals(
            $user1->getAssociate()->getMobilePhone(),
            $responseArr['associates']['0']['mobilePhone']
        );
        $this->assertEquals($user10->getAssociate()->getEmail(), $responseArr['associates']['9']['email']);
        $this->assertEquals(
            $user10->getAssociate()->getJoinDate()->format("Y-m-d"),
            $responseArr['associates']['9']['joinDate']
        );

        $this->assertEquals(3, $responseArr['pagination']['maxPages']);
        $this->assertEquals(1, $responseArr['pagination']['currentPage']);


        $client->request('GET', '/admin/api/associates', ['page' => '2']);

        $jsonResponse = $client->getResponse()->getContent();

        $responseArr = json_decode($jsonResponse, true);

        $this->assertEquals(10, sizeof($responseArr['associates']));

        $this->assertEquals($user11->getAssociate()->getId(), $responseArr['associates']['0']['id']);
        $this->assertEquals($user11->getAssociate()->getLevel(), $responseArr['associates']['0']['level']);
        $this->assertEquals($user11->getAssociate()->getFullName(), $responseArr['associates']['0']['fullName']);
        $this->assertEquals(
            $user11->getAssociate()->getMobilePhone(),
            $responseArr['associates']['0']['mobilePhone']
        );
        $this->assertEquals($user11->getAssociate()->getEmail(), $responseArr['associates']['0']['email']);
        $this->assertEquals(
            $user11->getAssociate()->getJoinDate()->format("Y-m-d"),
            $responseArr['associates']['0']['joinDate']
        );

        $this->assertEquals($user20->getAssociate()->getId(), $responseArr['associates']['9']['id']);
        $this->assertEquals($user20->getAssociate()->getLevel(), $responseArr['associates']['9']['level']);
        $this->assertEquals($user20->getAssociate()->getFullName(), $responseArr['associates']['9']['fullName']);
        $this->assertEquals(
            $user20->getAssociate()->getMobilePhone(),
            $responseArr['associates']['9']['mobilePhone']
        );
        $this->assertEquals($user20->getAssociate()->getEmail(), $responseArr['associates']['9']['email']);
        $this->assertEquals(
            $user20->getAssociate()->getJoinDate()->format("Y-m-d"),
            $responseArr['associates']['9']['joinDate']
        );

        $this->assertEquals(3, $responseArr['pagination']['maxPages']);
        $this->assertEquals(2, $responseArr['pagination']['currentPage']);

        $client->request('GET', '/admin/api/associates', ['page' => '3']);

        $jsonResponse = $client->getResponse()->getContent();

        $responseArr = json_decode($jsonResponse, true);

        $this->assertEquals(2, sizeof($responseArr['associates']));

        $this->assertEquals($user21->getAssociate()->getId(), $responseArr['associates']['0']['id']);
        $this->assertEquals($user21->getAssociate()->getLevel(), $responseArr['associates']['0']['level']);
        $this->assertEquals($user21->getAssociate()->getFullName(), $responseArr['associates']['0']['fullName']);
        $this->assertEquals(
            $user21->getAssociate()->getMobilePhone(),
            $responseArr['associates']['0']['mobilePhone']
        );
        $this->assertEquals($user21->getAssociate()->getEmail(), $responseArr['associates']['0']['email']);
        $this->assertEquals(
            $user21->getAssociate()->getJoinDate()->format("Y-m-d"),
            $responseArr['associates']['0']['joinDate']
        );

        $this->assertEquals($user22->getAssociate()->getId(), $responseArr['associates']['1']['id']);
        $this->assertEquals($user22->getAssociate()->getLevel(), $responseArr['associates']['1']['level']);
        $this->assertEquals($user22->getAssociate()->getFullName(), $responseArr['associates']['1']['fullName']);
        $this->assertEquals(
            $user22->getAssociate()->getMobilePhone(),
            $responseArr['associates']['1']['mobilePhone']
        );
        $this->assertEquals($user22->getAssociate()->getEmail(), $responseArr['associates']['1']['email']);
        $this->assertEquals(
            $user22->getAssociate()->getJoinDate()->format("Y-m-d"),
            $responseArr['associates']['1']['joinDate']
        );

        $this->assertEquals(3, $responseArr['pagination']['maxPages']);
        $this->assertEquals(3, $responseArr['pagination']['currentPage']);

        $client->request('GET', '/admin/api/associates', ['page' => '4']);

        $this->assertEquals(404, $client->getResponse()->getStatusCode());

        $this->assertContains(
            'Page 4 doesnt exist',
            $client->getResponse()->getContent()
        );

        $client->request('GET', '/admin/api/associates', ['page' => '-4']);

        $this->assertEquals(404, $client->getResponse()->getStatusCode());

        $this->assertContains(
            'Page -4 doesnt exist',
            $client->getResponse()->getContent()
        );

        $client->request('GET', '/admin/api/associates', ['page' => '-saf']);

        $this->assertEquals(404, $client->getResponse()->getStatusCode());

        $this->assertContains(
            'Page -saf doesnt exist',
            $client->getResponse()->getContent()
        );

        $client->request('GET', '/admin/api/associates', ['nameField' => 'S']);

        $jsonResponse = $client->getResponse()->getContent();

        $responseArr = json_decode($jsonResponse, true);

        $this->assertEquals(10, sizeof($responseArr['associates']));

        $this->assertEquals($user1->getAssociate()->getId(), $responseArr['associates']['0']['id']);
        $this->assertEquals($user1->getAssociate()->getLevel(), $responseArr['associates']['0']['level']);
        $this->assertEquals($user1->getAssociate()->getFullName(), $responseArr['associates']['0']['fullName']);
        $this->assertEquals(
            $user1->getAssociate()->getMobilePhone(),
            $responseArr['associates']['0']['mobilePhone']
        );
        $this->assertEquals($user1->getAssociate()->getEmail(), $responseArr['associates']['0']['email']);
        $this->assertEquals(
            $user1->getAssociate()->getJoinDate()->format("Y-m-d"),
            $responseArr['associates']['0']['joinDate']
        );

        $this->assertEquals($user13->getAssociate()->getId(), $responseArr['associates']['9']['id']);
        $this->assertEquals($user13->getAssociate()->getLevel(), $responseArr['associates']['9']['level']);
        $this->assertEquals($user13->getAssociate()->getFullName(), $responseArr['associates']['9']['fullName']);
        $this->assertEquals(
            $user13->getAssociate()->getMobilePhone(),
            $responseArr['associates']['9']['mobilePhone']
        );
        $this->assertEquals($user13->getAssociate()->getEmail(), $responseArr['associates']['9']['email']);
        $this->assertEquals(
            $user13->getAssociate()->getJoinDate()->format("Y-m-d"),
            $responseArr['associates']['9']['joinDate']
        );

        $this->assertEquals(2, $responseArr['pagination']['maxPages']);
        $this->assertEquals(1, $responseArr['pagination']['currentPage']);

        $client->request('GET', '/admin/api/associates', ['nameField' => 'S', 'page' => '2']);

        $jsonResponse = $client->getResponse()->getContent();

        $responseArr = json_decode($jsonResponse, true);

        $this->assertEquals(4, sizeof($responseArr['associates']));

        $this->assertEquals($user15->getAssociate()->getId(), $responseArr['associates']['0']['id']);
        $this->assertEquals($user15->getAssociate()->getLevel(), $responseArr['associates']['0']['level']);
        $this->assertEquals($user15->getAssociate()->getFullName(), $responseArr['associates']['0']['fullName']);
        $this->assertEquals(
            $user15->getAssociate()->getMobilePhone(),
            $responseArr['associates']['0']['mobilePhone']
        );
        $this->assertEquals($user15->getAssociate()->getEmail(), $responseArr['associates']['0']['email']);
        $this->assertEquals(
            $user15->getAssociate()->getJoinDate()->format("Y-m-d"),
            $responseArr['associates']['0']['joinDate']
        );

        $this->assertEquals($user22->getAssociate()->getId(), $responseArr['associates']['3']['id']);
        $this->assertEquals($user22->getAssociate()->getLevel(), $responseArr['associates']['3']['level']);
        $this->assertEquals($user22->getAssociate()->getFullName(), $responseArr['associates']['3']['fullName']);
        $this->assertEquals(
            $user22->getAssociate()->getMobilePhone(),
            $responseArr['associates']['3']['mobilePhone']
        );
        $this->assertEquals($user22->getAssociate()->getEmail(), $responseArr['associates']['3']['email']);
        $this->assertEquals(
            $user22->getAssociate()->getJoinDate()->format("Y-m-d"),
            $responseArr['associates']['3']['joinDate']
        );

        $this->assertEquals(2, $responseArr['pagination']['maxPages']);
        $this->assertEquals(2, $responseArr['pagination']['currentPage']);

        $client->request('GET', '/admin/api/associates', ['nameField' => 'Is']);

        $jsonResponse = $client->getResponse()->getContent();

        $responseArr = json_decode($jsonResponse, true);

        $this->assertEquals(4, sizeof($responseArr['associates']));

        $this->assertEquals($user3->getAssociate()->getId(), $responseArr['associates']['0']['id']);
        $this->assertEquals($user3->getAssociate()->getLevel(), $responseArr['associates']['0']['level']);
        $this->assertEquals($user3->getAssociate()->getFullName(), $responseArr['associates']['0']['fullName']);
        $this->assertEquals(
            $user3->getAssociate()->getMobilePhone(),
            $responseArr['associates']['0']['mobilePhone']
        );
        $this->assertEquals($user3->getAssociate()->getEmail(), $responseArr['associates']['0']['email']);
        $this->assertEquals(
            $user3->getAssociate()->getJoinDate()->format("Y-m-d"),
            $responseArr['associates']['0']['joinDate']
        );

        $this->assertEquals($user16->getAssociate()->getId(), $responseArr['associates']['3']['id']);
        $this->assertEquals($user16->getAssociate()->getLevel(), $responseArr['associates']['3']['level']);
        $this->assertEquals($user16->getAssociate()->getFullName(), $responseArr['associates']['3']['fullName']);
        $this->assertEquals(
            $user16->getAssociate()->getMobilePhone(),
            $responseArr['associates']['3']['mobilePhone']
        );
        $this->assertEquals($user16->getAssociate()->getEmail(), $responseArr['associates']['3']['email']);
        $this->assertEquals(
            $user16->getAssociate()->getJoinDate()->format("Y-m-d"),
            $responseArr['associates']['3']['joinDate']
        );

        $this->assertEquals(1, $responseArr['pagination']['maxPages']);
        $this->assertEquals(1, $responseArr['pagination']['currentPage']);


        $client->request('GET', '/admin/api/associates', ['nameField' => 'Is', 'emailField' => 'AU']);

        $jsonResponse = $client->getResponse()->getContent();

        $responseArr = json_decode($jsonResponse, true);

        $this->assertEquals(2, sizeof($responseArr['associates']));

        $this->assertEquals($user3->getAssociate()->getId(), $responseArr['associates']['0']['id']);
        $this->assertEquals($user3->getAssociate()->getLevel(), $responseArr['associates']['0']['level']);
        $this->assertEquals($user3->getAssociate()->getFullName(), $responseArr['associates']['0']['fullName']);
        $this->assertEquals(
            $user3->getAssociate()->getMobilePhone(),
            $responseArr['associates']['0']['mobilePhone']
        );
        $this->assertEquals($user3->getAssociate()->getEmail(), $responseArr['associates']['0']['email']);
        $this->assertEquals(
            $user3->getAssociate()->getJoinDate()->format("Y-m-d"),
            $responseArr['associates']['0']['joinDate']
        );

        $this->assertEquals($user4->getAssociate()->getId(), $responseArr['associates']['1']['id']);
        $this->assertEquals($user4->getAssociate()->getLevel(), $responseArr['associates']['1']['level']);
        $this->assertEquals($user4->getAssociate()->getFullName(), $responseArr['associates']['1']['fullName']);
        $this->assertEquals(
            $user4->getAssociate()->getMobilePhone(),
            $responseArr['associates']['1']['mobilePhone']
        );
        $this->assertEquals($user4->getAssociate()->getEmail(), $responseArr['associates']['1']['email']);
        $this->assertEquals(
            $user4->getAssociate()->getJoinDate()->format("Y-m-d"),
            $responseArr['associates']['1']['joinDate']
        );

        $this->assertEquals(1, $responseArr['pagination']['maxPages']);
        $this->assertEquals(1, $responseArr['pagination']['currentPage']);

        $client->request('GET', '/admin/api/associates', ['nameField' => 'Is',
            'emailField' => 'AU', 'telephoneField' => '2']);

        $jsonResponse = $client->getResponse()->getContent();

        $responseArr = json_decode($jsonResponse, true);

        $this->assertEquals(1, sizeof($responseArr['associates']));

        $this->assertEquals($user3->getAssociate()->getId(), $responseArr['associates']['0']['id']);
        $this->assertEquals($user3->getAssociate()->getLevel(), $responseArr['associates']['0']['level']);
        $this->assertEquals($user3->getAssociate()->getFullName(), $responseArr['associates']['0']['fullName']);
        $this->assertEquals(
            $user3->getAssociate()->getMobilePhone(),
            $responseArr['associates']['0']['mobilePhone']
        );
        $this->assertEquals($user3->getAssociate()->getEmail(), $responseArr['associates']['0']['email']);
        $this->assertEquals(
            $user3->getAssociate()->getJoinDate()->format("Y-m-d"),
            $responseArr['associates']['0']['joinDate']
        );

        $this->assertEquals(1, $responseArr['pagination']['maxPages']);
        $this->assertEquals(1, $responseArr['pagination']['currentPage']);

        $client->request('GET', '/admin/api/associates', ['nameField' => 'Isff',
            'emailField' => 'AU', 'telephoneField' => '2']);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $jsonResponse = $client->getResponse()->getContent();

        $responseArr = json_decode($jsonResponse, true);

        $this->assertEquals(0, sizeof($responseArr['associates']));

        $this->assertEquals(1, $responseArr['pagination']['maxPages']);
        $this->assertEquals(1, $responseArr['pagination']['currentPage']);
    }
}
