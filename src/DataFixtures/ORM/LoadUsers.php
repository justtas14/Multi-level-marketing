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
            $manager
        );
        $user2 = $this->createUser(
            '2',
            'associate@example.com',
            '1234',
            ['ROLE_USER'],
            'Lucy Tomlinson',
            $manager,
            $user1
        );

        $user3 = $this->createUser(
            '3',
            'SarahCunningham@dayrep.com',
            '1234',
            ['ROLE_USER'],
            'Sarah Cunningham',
            $manager,
            $user1
        );
        $user4 = $this->createUser(
            '4',
            'BaileyBrookes@dayrep.com',
            '1234',
            ['ROLE_USER'],
            'Bailey Brookes',
            $manager,
            $user2
        );
        $user5 = $this->createUser(
            '5',
            'AidanNewman@dayrep.com',
            '1234',
            ['ROLE_USER'],
            'Aidan Newman',
            $manager,
            $user2
        );
        $user6 = $this->createUser(
            '6',
            'NatashaHutchinson@rhyta.com',
            '1234',
            ['ROLE_USER'],
            'Natasha Hutchinson',
            $manager,
            $user3
        );
        $user7 = $this->createUser(
            '7',
            'JayPrice@rhyta.com',
            '1234',
            ['ROLE_USER'],
            'Jay Price',
            $manager,
            $user3
        );
        $user8 = $this->createUser(
            '8',
            'AaliyahLees@armyspy.com',
            '1234',
            ['ROLE_USER'],
            'Aaliyah Lees',
            $manager,
            $user5
        );
        $user9 = $this->createUser(
            '9',
            'LewisBenson@jourrapide.com',
            '1234',
            ['ROLE_USER'],
            'Lewis Benson',
            $manager,
            $user5
        );
        $user10 = $this->createUser(
            '10',
            'JoeChan@jourrapide.com',
            '1234',
            ['ROLE_USER'],
            'Joe Chan',
            $manager,
            $user6
        );
        $user11 = $this->createUser(
            '11',
            'JenniferGreen@jourrapide.com',
            '1234',
            ['ROLE_USER'],
            'Jennifer Green',
            $manager,
            $user7
        );
        $user12 = $this->createUser(
            '12',
            'SkyeReid@dayrep.com',
            '1234',
            ['ROLE_ADMIN','ROLE_USER'],
            'Skye Reid',
            $manager,
            $user7
        );
        $user13 = $this->createUser(
            '13',
            'TobyBarker@teleworm.us',
            '1234',
            ['ROLE_USER'],
            'Toby Barker',
            $manager,
            $user7
        );
        $user14 = $this->createUser(
            '14',
            'MichaelWard@dayrep.com',
            '1234',
            ['ROLE_USER'],
            'Michael Ward',
            $manager,
            $user8
        );
        $user15 = $this->createUser(
            '15',
            'SamanthaFaulkner@dayrep.com',
            '1234',
            ['ROLE_USER'],
            'Samantha Faulkner',
            $manager,
            $user8
        );
        $user16 = $this->createUser(
            '16',
            'AimeeWells@armyspy.com',
            '1234',
            ['ROLE_USER'],
            'Aimee Wells',
            $manager,
            $user8
        );
        $user17 = $this->createUser(
            '17',
            'AaronBull@rhyta.com',
            '1234',
            ['ROLE_USER'],
            'Aaron Bull',
            $manager,
            $user12
        );
        $user18 = $this->createUser(
            '18',
            'AbbieBarker@armyspy.com',
            '1234',
            ['ROLE_USER'],
            'Abbie Barker',
            $manager,
            $user12
        );
        $user19 = $this->createUser(
            '19',
            'HaydenPower@teleworm.us',
            '1234',
            ['ROLE_USER'],
            'Hayden Power',
            $manager,
            $user15
        );
        $user20 = $this->createUser(
            '20',
            'ElizabethLeonard@dayrep.com',
            '1234',
            ['ROLE_USER'],
            'Elizabeth Leonard',
            $manager,
            $user16
        );
        $user21 = $this->createUser(
            '21',
            'ArchieWatts@rhyta.com',
            '1234',
            ['ROLE_USER'],
            'Archie Watts',
            $manager,
            $user17
        );
        $user22 = $this->createUser(
            '22',
            'IsabelleMoran@jourrapide.com',
            '1234',
            ['ROLE_USER'],
            'Isabelle Moran',
            $manager,
            $user17
        );

        $manager->flush();
    }
    private function createUser($nr, $email, $plainPassword, $roles, $fullName, $manager, $parent = null)
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
