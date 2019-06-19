<?php


namespace App\Tests\Controller;

use App\Entity\EmailTemplate;
use App\Entity\User;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\ORM\EntityManager;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class AdminControllerEmailTest extends WebTestCase
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
            "App\DataFixtures\ORM\LoadUsers",
        ])->getReferenceRepository();
    }

    /**
     *  Testing fetcth email template service if it creates new default appropriate email template if it doesnt exist
     *
     *  - Request to email template with a slug parameter invitation.
     *  - Expected to have new default invitation email template to be created in database.
     *
     *  - Request to email template with a slug parameter welcome.
     *  - Expected to have new default welcome email template to be created in database.
     *
     *  - Request to email template with a wrong slug parameter.
     *  - Expected to have error and 404 not found exception to be thrown.
     */
    public function testFetchEmail()
    {
        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var User $user */
        $user = $this->fixtures->getReference('user1');

        $em->refresh($user);
        $this->loginAs($user, 'main');

        $client = $this->makeClient();

        $client->request('GET', '/admin/emailtemplate/invitation');

        $emailTemplate = $em->getRepository(EmailTemplate::class)->findOneBy(['emailType' => 'INVITATION']);

        $this->assertEquals("You got invited by {{senderName}}. ", $emailTemplate->getEmailSubject());
        $this->assertEquals("<br/> Here is your link {{link}} <br/><br/>", $emailTemplate->getEmailBody());
        $this->assertEquals("INVITATION", $emailTemplate->getEmailType());

        $client->request('GET', '/admin/emailtemplate/password');

        $emailTemplate = $em->getRepository(EmailTemplate::class)
            ->findOneBy(['emailType' => 'RESET_PASSWORD']);

        $this->assertEquals("Password Reset", $emailTemplate->getEmailSubject());
        $this->assertEquals(
            'To reset your password click here <a href="{{ link }}">{{ link }}</a><br/><br/>',
            $emailTemplate->getEmailBody()
        );
        $this->assertEquals("RESET_PASSWORD", $emailTemplate->getEmailType());

        $client->request('GET', '/admin/emailtemplate/welcome');

        $emailTemplate = $em->getRepository(EmailTemplate::class)
            ->findOneBy(['emailType' => 'WELCOME']);

        $this->assertEquals("Welcome", $emailTemplate->getEmailSubject());
        $this->assertEquals(
            'Hello {{ name }}, welcome to prelaunch!',
            $emailTemplate->getEmailBody()
        );
        $this->assertEquals("WELCOME", $emailTemplate->getEmailType());

        $client->request('GET', '/admin/emailtemplate/wrong');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}
