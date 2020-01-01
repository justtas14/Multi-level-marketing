<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\Reusables\LoginOperations;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\ORM\EntityManager;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class PureAdminTest extends WebTestCase
{
    use LoginOperations;

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

        $this->loginAs($user, 'main');

        $client = $this->makeClient();

        $jwtManager = $client->getContainer()->get('pts_user.jwt.manager');

        $token = $this->getToken($jwtManager, $user);

        $client->xmlHttpRequest(
            'GET',
            '/api/admin/explorer',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

        $jsonResponse = $client->getResponse()->getContent();

        $responseArr = json_decode($jsonResponse, true);

        $this->assertEquals(4, sizeof($responseArr));

        $this->assertEquals('-1', $responseArr['id']);
        $this->assertEquals("Company", $responseArr['title']);
        $this->assertEquals('-2', $responseArr['parentId']);
        $this->assertEquals('1', $responseArr['numberOfChildren']);

        $client->xmlHttpRequest(
            'GET',
            '/api/admin/explorer',
            ['id' => '3'],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

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
}
