<?php

namespace App\DataFixtures\ORM;

use App\Entity\Invitation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadInvitations extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $fullNames = ['justas', 'jonas', 'petras', 'antanas', 'aluyzas', 'martynas'];
        $emails = ['justas@gmail.com', 'jonas@gmail.com', 'petras@gmail.com',
            'antanas@gmail.com', 'aluyzas@gmail.com', 'martynas@gmail.com'];
        $senders = [
            $this->getReference('user1'),
            $this->getReference('user4'),
            $this->getReference('user12'),
            $this->getReference('user4'),
            $this->getReference('user7'),
            $this->getReference('user10')
        ];
        for ($i = 0; $i < 6; $i++) {
            $manager->refresh($senders[$i]);
        }

        $used = [true, false, false, true, false, false];
        $created = [false, false, 100, 250, false, false];
        $times = [104,484,15,478,949,14];

        for ($i = 0; $i < 6; $i++) {
            $invitation = new Invitation();
            $invitation->setFullName($fullNames[$i]);
            $invitation->setEmail($emails[$i]);
            $invitation->setSender($senders[$i]->getAssociate());
            $invitation->setUsed($used[$i]);
            if ($created[$i]) {
                $invitation->setCreated($created[$i]);
            }
            $invitation->setInvitationCode(md5($times[$i]));
            $manager->persist($invitation);

            $this->addReference('invitation' . ($i+1), $invitation);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            LoadUsers::class,
            LoadEmailTemplates::class
        ];
    }
}
