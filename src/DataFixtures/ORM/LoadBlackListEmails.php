<?php

namespace App\DataFixtures\ORM;

use App\Entity\InvitationBlacklist;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadBlackListEmails extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $invitationBlackListEmails = new InvitationBlacklist();

        $invitationBlackListEmails->setId(null);
        $invitationBlackListEmails->setEmail('james@gmail.com');

        $manager->persist($invitationBlackListEmails);
        $this->addReference('invitationBlackListEmail', $invitationBlackListEmails);

        $manager->flush();
    }
}
