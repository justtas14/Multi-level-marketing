<?php

namespace App\DataFixtures\ORM;

use App\Entity\EmailTemplate;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadEmailTemplates extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $emailTemplate = new EmailTemplate();

        $emailTemplate->setId(null);
        $emailTemplate->setEmailType('INVITATION');
        $emailTemplate->setEmailSubject("You got invited by {{senderName}}. ");
        $emailTemplate->setEmailBody("<br/> Here is your <a href='{{link}}'>link</a> <br/><br/>".
            "To opt out of this service click <a href='{{ optOutUrl }}'>this</a> link");

        $manager->persist($emailTemplate);
        $this->addReference('emailTemplateDefault', $emailTemplate);

        $manager->flush();
    }
}
