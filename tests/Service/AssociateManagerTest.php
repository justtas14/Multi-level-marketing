<?php


namespace App\Tests\Service;

use App\Entity\Associate;
use App\Entity\User;
use App\Exception\GetAllDirectAssociatesException;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;

class AssociateManagerTest extends WebTestCase
{
    /**
     * @var ReferenceRepository
     */
    private $fixtures;

    /**
     * @group legacy
     */
    protected function setUp()
    {
        $this->fixtures = $this->loadFixtures([
            "App\DataFixtures\ORM\LoadUsers"
        ])->getReferenceRepository();
    }

    /**
     *  Testing getNumberOfLevels service method whether it returns correct number of levels
     *
     *  - Call associateManager service getNumberOfLevels method without any params.
     *  - Expected to return '5' number of levels as this level is max level of the tree.
     * Without params getNumberOfLevels method expected to return max level of the tree.
     *
     *  - Call associateManager service getNumberOfLevels method with associateId of associate with id 2 in params.
     *  - Expected to return '4' number of levels which is correct because getNumberOfLevels returns numberOfLevels
     * by this formula: (maxNumberOfLevels in associate downline) - (associateLevel).
     * There is '5' max level in id 2 associate downline and 2 id associate level is 1, so: 5 - 1 = 4.
     *
     *  - Call associateManager service getNumberOfLevels method with associateId of associate with id 19 in params.
     *  - Expected to return '0' number of levels which is correct because getNumberOfLevels returns numberOfLevels
     * by this formula: (maxNumberOfLevels in associate downline) - (associateLevel).
     * There is '5' max level in id 19 associate downline and 19 id associate level is 5, so: 5 - 5 = 0.
     *
     * - Call associateManager service getNumberOfLevels method with associateId of associate with id 6 in params.
     * - Expected to return '1' number of levels which is correct because getNumberOfLevels returns numberOfLevels
     * by this formula: (maxNumberOfLevels in associate downline) - (associateLevel).
     * There is '3' max level in id 6 associate downline and 6 id associate level is 2, so: 3 - 2 = 1.
     */
    public function testGetNumberOfLevels()
    {
        $container = $this->getContainer();
        $associateManager = $container->get('App\Service\AssociateManager');

        $em = $this->fixtures->getManager();

        $numberOfLevels = $associateManager->getNumberOfLevels();

        $this->assertEquals(5, $numberOfLevels);

        $associate = $em->find(Associate::class, 2);

        $numberOfLevels = $associateManager->getNumberOfLevels($associate->getAssociateId());

        $this->assertEquals(4, $numberOfLevels);

        $associate = $em->find(Associate::class, 19);

        $numberOfLevels = $associateManager->getNumberOfLevels($associate->getAssociateId());

        $this->assertEquals(0, $numberOfLevels);

        $associate = $em->find(Associate::class, 6);

        $numberOfLevels = $associateManager->getNumberOfLevels($associate->getAssociateId());

        $this->assertEquals(1, $numberOfLevels);
    }

    /**
     *  Testing getNumberOfAssociatesInDownline service method whether it returns correct number
     * of associates from associate in appropriate level
     *
     *  - Call associateManager service getNumberOfAssociatesInDownline method 5 times as this is a max level
     * in the entire tree. Associate with id 1 is also admin, so it returns all associates in each level
     * Parameters to this method given: associateId of associate with id 1 and iterative $i which is a level each time
     * incrementing by 1 until 5 which is a max level
     * Testing associate number in each of associate id 1 level.
     *  - Expected to return [2,4,6,5,4] number of associates in appropriate $i iterative level.
     *
     *  - Call associateManager service getNumberOfAssociatesInDownline method 3 times as this is a max level
     * in downline of associate id 7.
     * Parameters to this method given: associateId of associate with id 7 and iterative $i which is a level each time
     * incrementing by 1 until 3 which is a max level in downline of associate id 7.
     * Testing associate number in each of associate id 7 level.
     *  - Expected to return [3,2,2] number of associates in appropriate $i iterative level.
     *
     *  - Call associateManager service getNumberOfAssociatesInDownline method 1 times as this is a max level
     * in downline of associate id 6.
     * Parameters to this method given: associateId of associate with id 6 and iterative $i which is a level each time
     * incrementing by 1 until 1 which is a max level in downline of associate id 6.
     * Testing associate number in each of associate id 6 level.
     *  - Expected to return [1] number of associates in appropriate $i iterative level.
     *
     *  - Call associateManager service getNumberOfAssociatesInDownline method 0 times as this is a max level
     * in downline of associate id 9.
     * Parameters to this method given: associateId of associate with id 9 and iterative $i which is a level each time
     * incrementing by 1 until 0 which is a max level in downline of associate id 9.
     * Testing associate number in each of associate id 9 level.
     *  - Expected to return [] number of associates in appropriate $i iterative level.
     */
    public function testGetNumberOfAssociatesInDownline()
    {
        $container = $this->getContainer();

        $associateManager = $container->get('App\Service\AssociateManager');

        $em = $this->fixtures->getManager();

        $associate = $em->find(Associate::class, 1);

        $assertExpectedValues = [2,4,6,5,4];

        for ($i = 1; $i <= sizeof($assertExpectedValues); $i++) {
            $numberOfAssociates = $associateManager->getNumberOfAssociatesInDownline($i);
            $this->assertEquals($assertExpectedValues[$i-1], $numberOfAssociates);
        }

        $associate = $em->find(Associate::class, 7);


        $assertExpectedValues = [3,2,2];

        for ($i = 1; $i <= sizeof($assertExpectedValues); $i++) {
            $numberOfAssociates = $associateManager->getNumberOfAssociatesInDownline($i, $associate->getAssociateId());
            $this->assertEquals($assertExpectedValues[$i-1], $numberOfAssociates);
        }

        $associate = $em->find(Associate::class, 6);

        $assertExpectedValues = [1];

        for ($i = 1; $i <= sizeof($assertExpectedValues); $i++) {
            $numberOfAssociates = $associateManager->getNumberOfAssociatesInDownline($i, $associate->getAssociateId());
            $this->assertEquals($assertExpectedValues[$i-1], $numberOfAssociates);
        }

        $associate = $em->find(Associate::class, 9);

        $assertExpectedValues = [];

        for ($i = 1; $i <= sizeof($assertExpectedValues); $i++) {
            $numberOfAssociates = $associateManager->getNumberOfAssociatesInDownline($i, $associate->getAssociateId());
            $this->assertEquals($assertExpectedValues[$i-1], $numberOfAssociates);
        }
    }

    private function login(UserInterface $user)
    {
        $container = $this->getContainer();

        $newToken = new UsernamePasswordToken($user, null, 'main', $user->getRoles());

        $container->get('security.token_storage')->setToken($newToken);
    }

    /**
     *  Testing getAllDirectAssociates service method whether it returns correct number of associates
     * when parent id is given
     *
     *  - Call associateManager service getAllDirectAssociates method with associateId of associate id 3 in params
     * and logged in as user3 who isnt admin.
     *  - Expected to return '2' number of associates which is correct parent associate with id 3 has 2 children
     *
     *  - Call associateManager service getAllDirectAssociates method with associateId of associate id 16 in params
     * and logged in as user 16 who isnt admin.
     *  - Expected to return '1' number of associates which is correct parent associate with id 16 has 1 children
     *
     *  - Call associateManager service getAllDirectAssociates method with associateId of associate id 21 in params
     * and logged in as user 21 who isnt admin.
     *  - Expected to return '0' number of associates which is correct parent associate with id 16 has 0 children
     *
     *  - Call associateManager service getAllDirectAssociates method with associateId of associate id 7 in params
     * and logged in as user 5 which isnt admin.
     *  - Expected exception to be thrown because current logged in is associate with id 5 and user tried to get all
     * direct associates of associate with id 7 which is not in associate with id 5 downline.
     * Also user with id 5 is not admin so associate 7 cant be reached if it's not in user 5 downline
     */
    public function testGetAllDirectAssociates()
    {
        $container = $this->getContainer();
        $associateManager = $container->get('App\Service\AssociateManager');

        $em = $this->fixtures->getManager();

        $associate = $em->find(Associate::class, 3);

        /** @var User $user */
        $user = $this->fixtures->getReference('user3');
        $this->login($user);

        $numberOfDirectAssociates = $associateManager->getAllDirectAssociates($associate->getAssociateId());

        $this->assertEquals(2, sizeof($numberOfDirectAssociates));

        $associate = $em->find(Associate::class, 16);

        /** @var User $user */
        $user = $this->fixtures->getReference('user16');
        $this->login($user);

        $numberOfDirectAssociates = $associateManager->getAllDirectAssociates($associate->getAssociateId());

        $this->assertEquals(1, sizeof($numberOfDirectAssociates));

        $associate = $em->find(Associate::class, 21);

        /** @var User $user */
        $user = $this->fixtures->getReference('user21');
        $this->login($user);

        $numberOfDirectAssociates = $associateManager->getAllDirectAssociates($associate->getAssociateId());

        $this->assertEquals(0, sizeof($numberOfDirectAssociates));

        $associate = $em->find(Associate::class, 7);

        /** @var User $user */
        $user = $this->fixtures->getReference('user5');
        $this->login($user);

        $this->expectException(GetAllDirectAssociatesException::class);

        $associateManager->getAllDirectAssociates($associate->getAssociateId());
    }

    /** Testing isAncestor service method whether associate is ancestor in user downline
     *
     *  - Call associateManager service isAncestor method with associateId of associate id 5 as user and
     * associateId of associate id 15 as parent in params.
     *  - Expected to return true because parent associate of id 15 is ancestor of user associate of id 5
     *
     *  - Call associateManager service isAncestor method with associateId of associate id 5 as user and
     * associateId of associate id 5 as parent in params.
     *  - Expected to return true because parent associate of id 5 matches user associate of id 5
     *
     *  - Call associateManager service isAncestor method with associateId of associate id 5 as user and
     * associateId of associate id 2 as parent in params.
     *  - Expected to return false because parent associate of id 2 is not ancestor of user associate of id 5
     * or user is matched to parent
     */
    public function testIsAncestor()
    {
        $container = $this->getContainer();
        $associateManager = $container->get('App\Service\AssociateManager');

        $em = $this->fixtures->getManager();

        $userAssociate = $em->find(Associate::class, 5);

        $parentAssociate = $em->find(Associate::class, 15);

        $isAncestor = $associateManager
            ->isAncestor($parentAssociate->getAssociateId(), $userAssociate->getAssociateId());

        $this->assertTrue($isAncestor);

        $userAssociate = $em->find(Associate::class, 5);

        $parentAssociate = $em->find(Associate::class, 5);

        $isAncestor = $associateManager
            ->isAncestor($parentAssociate->getAssociateId(), $userAssociate->getAssociateId());

        $this->assertTrue($isAncestor);

        $userAssociate = $em->find(Associate::class, 5);

        $parentAssociate = $em->find(Associate::class, 2);

        $isAncestor = $associateManager
            ->isAncestor($parentAssociate->getAssociateId(), $userAssociate->getAssociateId());

        $this->assertFalse($isAncestor);
    }
}
