<?php

namespace App\DataFixtures\ORM;

use App\Entity\User;
use App\Entity\Associate;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadUsers extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user1 = $this->createUser(
            '1',
            'admin@plumtreesystems.com',
            '123456789',
            ['ROLE_ADMIN', 'ROLE_USER'],
            'Connor Vaughan',
            '12455412',
            $manager
        );
        $user2 = $this->createUser(
            '2',
            'associate@example.com',
            '1234',
            ['ROLE_USER'],
            'Lucy Tomlinson',
            '44864',
            $manager,
            $user1
        );

        $user3 = $this->createUser(
            '3',
            'SarahCunningham@dayrep.com',
            '1234',
            ['ROLE_USER'],
            'Sarah Cunningham',
            '254345',
            $manager,
            $user1
        );
        $user4 = $this->createUser(
            '4',
            'BaileyBrookes@dayrep.com',
            '1234',
            ['ROLE_USER'],
            'Bailey Brookes',
            '14546',
            $manager,
            $user2
        );
        $user5 = $this->createUser(
            '5',
            'AidanNewman@dayrep.com',
            '1234',
            ['ROLE_USER'],
            'Aidan Newman',
            '45454',
            $manager,
            $user2
        );
        $user6 = $this->createUser(
            '6',
            'NatashaHutchinson@rhyta.com',
            '1234',
            ['ROLE_USER'],
            'Natasha Hutchinson',
            '865483621',
            $manager,
            $user3
        );
        $user7 = $this->createUser(
            '7',
            'JayPrice@rhyta.com',
            '1234',
            ['ROLE_USER'],
            'Jay Price',
            '122545',
            $manager,
            $user3
        );
        $user8 = $this->createUser(
            '8',
            'AaliyahLees@armyspy.com',
            '1234',
            ['ROLE_USER'],
            'Aaliyah Lees',
            '24534543',
            $manager,
            $user5
        );
        $user9 = $this->createUser(
            '9',
            'LewisBenson@jourrapide.com',
            '1234',
            ['ROLE_USER'],
            'Lewis Benson',
            '152543',
            $manager,
            $user5
        );
        $user10 = $this->createUser(
            '10',
            'JoeChan@jourrapide.com',
            '1234',
            ['ROLE_USER'],
            'Joe Chan',
            '21524152',
            $manager,
            $user6
        );
        $user11 = $this->createUser(
            '11',
            'JenniferGreen@jourrapide.com',
            '1234',
            ['ROLE_USER'],
            'Jennifer Green',
            '214152',
            $manager,
            $user7
        );
        $user12 = $this->createUser(
            '12',
            'SkyeReid@dayrep.com',
            '1234',
            ['ROLE_ADMIN','ROLE_USER'],
            'Skye Reid',
            '2152',
            $manager,
            $user7
        );
        $user13 = $this->createUser(
            '13',
            'TobyBarker@teleworm.us',
            '1234',
            ['ROLE_USER'],
            'Toby Barker',
            '1441',
            $manager,
            $user7
        );
        $user14 = $this->createUser(
            '14',
            'MichaelWard@dayrep.com',
            '1234',
            ['ROLE_USER'],
            'Michael Ward',
            '54578',
            $manager,
            $user8
        );
        $user15 = $this->createUser(
            '15',
            'SamanthaFaulkner@dayrep.com',
            '1234',
            ['ROLE_USER'],
            'Samantha Faulkner',
            '12453',
            $manager,
            $user8
        );
        $user16 = $this->createUser(
            '16',
            'AimeeWells@armyspy.com',
            '1234',
            ['ROLE_USER'],
            'Aimee Wells',
            '144114',
            $manager,
            $user8
        );
        $user17 = $this->createUser(
            '17',
            'AaronBull@rhyta.com',
            '1234',
            ['ROLE_USER'],
            'Aaron Bull',
            '25412423',
            $manager,
            $user12
        );
        $user18 = $this->createUser(
            '18',
            'AbbieBarker@armyspy.com',
            '1234',
            ['ROLE_USER'],
            'Abbie Barker',
            '254123',
            $manager,
            $user12
        );
        $user19 = $this->createUser(
            '19',
            'HaydenPower@teleworm.us',
            '1234',
            ['ROLE_USER'],
            'Hayden Power',
            '14214',
            $manager,
            $user15
        );
        $user20 = $this->createUser(
            '20',
            'ElizabethLeonard@dayrep.com',
            '1234',
            ['ROLE_USER'],
            'Elizabeth Leonard',
            '1447214',
            $manager,
            $user16
        );
        $user21 = $this->createUser(
            '21',
            'ArchieWatts@rhyta.com',
            '1234',
            ['ROLE_USER'],
            'Archie Watts',
            '14414',
            $manager,
            $user17
        );
        $user22 = $this->createUser(
            '22',
            'IsabelleMoran@jourrapide.com',
            '1234',
            ['ROLE_USER'],
            'Isabelle Moran',
            '144147',
            $manager,
            $user17
        );

        $manager->flush();
    }
    private function createUser($nr, $email, $plainPassword, $roles, $fullName, $telephone, $manager, $parent = null)
    {
        $user = new User();
        $associate = new Associate();

        $user->setId($nr);
        $user->setEmail($email);
        $user->setPlainPassword($plainPassword);
        $user->setRoles($roles);

        $associate->setId($nr);
        $associate->setFullName($fullName);
        $associate->setEmail($email);
        $associate->setMobilePhone($telephone);
        /** @var User $parent */
        if ($parent) {
            $associate->setParent($parent->getAssociate());
        }
        $user->setAssociate($associate);

        /** @var ObjectManager $manager */
        $manager->persist($associate);
        $manager->persist($user);

        $this->addReference('user' . $nr, $user);

        return $user;
    }
}
