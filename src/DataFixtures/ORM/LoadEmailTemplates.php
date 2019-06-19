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
        $this->addReference('emailTemplateInvitation', $emailTemplate);

        $emailTemplate = new EmailTemplate();

        $emailTemplate->setId(null);
        $emailTemplate->setEmailType('RESET_PASSWORD');
        $emailTemplate->setEmailSubject("Password Reset");
        $emailTemplate->setEmailBody(
            'To reset your password click here <a href="{{ link }}">{{ link }}</a><br/><br/>'
        );

        $manager->persist($emailTemplate);
        $this->addReference('emailTemplateResetPassword', $emailTemplate);

        $emailTemplate = new EmailTemplate();

        $emailTemplate->setId(null);
        $emailTemplate->setEmailType('WELCOME');
        $emailTemplate->setEmailSubject("Welcome");
        $emailTemplate->setEmailBody(
            'Hello {{ name }}, welcome to prelaunch!'
        );

        $manager->persist($emailTemplate);
        $this->addReference('emailTemplateWelcome', $emailTemplate);

        $manager->flush();
    }
}
