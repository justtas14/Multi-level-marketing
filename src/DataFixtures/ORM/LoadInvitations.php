<?php

namespace App\DataFixtures\ORM;

use App\Entity\User;
use App\Entity\Associate;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadInvitations //extends Fixture
{
//    public function load(ObjectManager $manager)
//    {
//        $user1 = $this->createUser(
//            '1',
//            'justtas14@gmail.com',
//            'justtas14',
//            ['ROLE_ADMIN', 'ROLE_USER'],
//            'Justas',
//            $manager
//        );
//        $user2 = $this->createUser(
//            '2',
//            'vanagas@gmail.com',
//            'vanagas',
//            ['ROLE_USER'],
//            'Vanagas',
//            $manager,
//            $user1
//        );
//
//        $user3 = $this->createUser(
//            '3',
//            'paukstis@gmail.com',
//            'paukstis',
//            ['ROLE_USER'],
//            'Paukstis',
//            $manager,
//            $user1
//        );
//        $user4 = $this->createUser(
//            '4',
//            'draustinis@gmail.com',
//            'draustinis',
//            ['ROLE_USER'],
//            'Draustinis',
//            $manager,
//            $user2
//        );
//        $user5 = $this->createUser(
//            '5',
//            'dangus@gmail.com',
//            'dangus',
//            ['ROLE_USER'],
//            'Dangus',
//            $manager,
//            $user2
//        );
//        $user6 = $this->createUser(
//            '6',
//            'rankena@gmail.com',
//            'rankena',
//            ['ROLE_USER'],
//            'Rankena',
//            $manager,
//            $user3
//        );
//        $user7 = $this->createUser(
//            '7',
//            'lempa@gmail.com',
//            'lempa',
//            ['ROLE_USER'],
//            'Lempa',
//            $manager,
//            $user3
//        );
//        $user8 = $this->createUser(
//            '8',
//            'uzuolaida@gmail.com',
//            'uzuolaida',
//            ['ROLE_USER'],
//            'Uzuolaida',
//            $manager,
//            $user5
//        );
//        $user9 = $this->createUser(
//            '9',
//            'skritulys@gmail.com',
//            'skritulys',
//            ['ROLE_USER'],
//            'Skritulys',
//            $manager,
//            $user5
//        );
//        $user10 = $this->createUser(
//            '10',
//            'puodas@gmail.com',
//            'puodas',
//            ['ROLE_USER'],
//            'Puodas',
//            $manager,
//            $user6
//        );
//        $user11 = $this->createUser(
//            '11',
//            'draugas@gmail.com',
//            'draugas',
//            ['ROLE_USER'],
//            'Draugas',
//            $manager,
//            $user7
//        );
//        $user12 = $this->createUser(
//            '12',
//            'priesas@gmail.com',
//            'priesas',
//            ['ROLE_USER'],
//            'Priesas',
//            $manager,
//            $user7
//        );
//        $user13 = $this->createUser(
//            '13',
//            'kompas@gmail.com',
//            'kompas',
//            ['ROLE_USER'],
//            'Kompas',
//            $manager,
//            $user7
//        );
//        $user14 = $this->createUser(
//            '14',
//            'wifi@gmail.com',
//            'wifi',
//            ['ROLE_USER'],
//            'Wifi',
//            $manager,
//            $user8
//        );
//        $user15 = $this->createUser(
//            '15',
//            'penktadienis@gmail.com',
//            'penktadienis',
//            ['ROLE_USER'],
//            'Penktadienis',
//            $manager,
//            $user8
//        );
//        $user16 = $this->createUser(
//            '16',
//            'veidrodis@gmail.com',
//            'veidrodis',
//            ['ROLE_USER'],
//            'Veidrodis',
//            $manager,
//            $user8
//        );
//        $user17 = $this->createUser(
//            '17',
//            'ekecia@gmail.com',
//            'ekecia',
//            ['ROLE_USER'],
//            'Ekecia',
//            $manager,
//            $user12
//        );
//        $user18 = $this->createUser(
//            '18',
//            'baterija@gmail.com',
//            'baterija',
//            ['ROLE_USER'],
//            'Baterija',
//            $manager,
//            $user12
//        );
//        $user19 = $this->createUser(
//            '19',
//            'suva@gmail.com',
//            'suva',
//            ['ROLE_USER'],
//            'Suva',
//            $manager,
//            $user15
//        );
//        $user20 = $this->createUser(
//            '20',
//            'kate@gmail.com',
//            'kate',
//            ['ROLE_USER'],
//            'kate',
//            $manager,
//            $user16
//        );
//        $user21 = $this->createUser(
//            '21',
//            'diena@gmail.com',
//            'diena',
//            ['ROLE_USER'],
//            'diena',
//            $manager,
//            $user17
//        );
//        $user22 = $this->createUser(
//            '22',
//            'horizontas@gmail.com',
//            'horizontas',
//            ['ROLE_USER'],
//            'horizontas',
//            $manager,
//            $user17
//        );
//
//        $manager->flush();
//    }
//    private function createUser($nr, $email, $plainPassword, $roles, $fullName, $manager, $parent = null)
//    {
//        $user = new User();
//        $associate = new Associate();
//
//        $user->setId($nr);
//        $user->setEmail($email);
//        $user->setPlainPassword($plainPassword);
//        $user->setRoles($roles);
//
//        $associate->setId($nr);
//        $associate->setFullName($fullName);
//        $associate->setEmail($email);
//        /** @var User $parent */
//        if ($parent) {
//            $associate->setParent($parent->getAssociate());
//        }
//        $user->setAssociate($associate);
//
//        /** @var ObjectManager $manager */
//        $manager->persist($associate);
//        $manager->persist($user);
//
//        $this->addReference('user' . $nr, $user);
//
//        return $user;
//    }
}
