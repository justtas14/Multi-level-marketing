<?php


namespace App\Tests\Controller;

use App\Entity\EmailTemplate;
use App\Entity\Invitation;
use App\Entity\User;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\ORM\EntityManager;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
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
            "App\DataFixtures\ORM\LoadEmailTemplates"
        ])->getReferenceRepository();
    }

    /**
     *  Testing send method functionality if it sends correctly and content is the same as expected
     *
     *  - Send invitation with new created invitation which has 4 atributes:
     * Sender: user which id is 1
     * Email: 'myemail@gmail.com'
     * Full name: 'myemail'
     * InvitationCode: random generated code
     *  - Expected to get one email with appropriate subject. Expected invitation entity to be added in database.
     * Email expected to get from sender email.
     * Email expected to sent to invitation set email.
     * Email expected to contain in body this text: 'You got invited by', sender full name
     * and generated link with appropriate invitation code.
     *
     *
     */
    public function testMailIsSentAndContentIsOk()
    {
        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var User $user */
        $user = $this->fixtures->getReference('user1');

        $em->refresh($user);
        $this->loginAs($user, 'main');

        $client = $this->makeClient();

        $crawler = $client->request('GET', '/associate/invite');

        $form = $crawler->selectButton('Send')->form();

        $form->get('invitation')['email']->setValue('myemail@gmail.com');
        $form->get('invitation')['fullName']->setValue('myemail');

        $client->enableProfiler();

        $invitationRepository = $em->getRepository(Invitation::class);

        $invitations = $invitationRepository->findAll();

        $this->assertEquals(0, sizeof($invitations));

        $client->submit($form);

        $invitations = $invitationRepository->findAll();

        $this->assertEquals(1, sizeof($invitations));

        $mailCollector = $client->getProfile()->getCollector('swiftmailer');

        $this->assertSame(1, $mailCollector->getMessageCount());

        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];

        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertSame('You got invited by Connor Vaughan. ', $message->getSubject());
        $this->assertSame("noreply@plumtreesystems.com", key($message->getFrom()));
        $this->assertSame('myemail@gmail.com', key($message->getTo()));
    }


    /**
     *  Testing end prelaunch feature
     *
     *  - Login as admin and go to end prelaunch form, set end prelaunch to false and submit. Then login with
     * different not admin user.
     *  - Expected to be redirected in associate page after requests to '/' and '/associate' pages. Also expected
     * redirection then requested to '/landingpage' because landing page is not ended.
     *
     *  - Login as admin and go to end prelaunch form, set end prelaunch to true and submit. Admin isn't redirected
     * to landing page. Then login with different not admin user.
     *  - Expected to be redirected in landing page after requests to '/' and '/associate' pages. Also expected not
     * to be redirected then requested to '/landingpage' because prelaunch is ended.
     */
    public function testEndPrelaunch()
    {
        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var User $user */
        $user = $this->fixtures->getReference('user1');

        $em->refresh($user);
        $this->loginAs($user, 'main');

        $client = $this->makeClient();

        $crawler = $client->request('GET', '/admin/endprelaunch');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Save')->form();

        $form->get('end_prelaunch')['prelaunchEnded']->setValue(false);

        $client->submit($form);

        $client->request('GET', '/logout');

        $client->enableProfiler();

        $client->request(
            'POST',
            '/login',
            ['submit' => true, '_username' => 'associate@example.com', '_password' => '1234']
        );

        $client->request('GET', '/');

        $targetUrl = $client->getResponse()->isRedirect("/associate");

        $this->assertTrue($targetUrl);

        $client->request('GET', '/associate');

        $isRedirection = $client->getResponse()->isRedirection();

        $this->assertFalse($isRedirection);

        $client->request('GET', '/landingpage');

        $targetUrl = $client->getResponse()->isRedirect("/");

        $this->assertTrue($targetUrl);

        $client->request('GET', '/logout');

        $client->request(
            'POST',
            '/login',
            ['submit' => true, '_username' => 'admin@example.com', '_password' => '1234']
        );

        $crawler = $client->request('GET', '/admin/endprelaunch');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Save')->form();

        $form->get('end_prelaunch')['prelaunchEnded']->setValue(true);

        $client->submit($form);

        $client->request('GET', '/');

        $targetUrl = $client->getResponse()->isRedirect("/admin");

        $this->assertTrue($targetUrl);

        $client->request('GET', '/logout');

        $client->request(
            'POST',
            '/login',
            ['submit' => true, '_username' => 'associate@example.com', '_password' => '1234']
        );

        $client->request('GET', '/');

        $client->getResponse()->isRedirect("/landingpage");

        $client->request('GET', '/associate');

        $targetUrl = $client->getResponse()->isRedirect("/landingpage");

        $this->assertTrue($targetUrl);

        $client->request('GET', '/landingpage');

        $targetUrl = $client->getResponse()->isRedirection();

        $this->assertFalse($targetUrl);
    }

    /**
     *  Testing email template form functionality
     *
     * - Request to email templates page when logged in as admin and expected to find one emailTemplate with
     * appropriate params. Change form values and submit.
     * - Expected that emailTemplate values have updated in database.
     */
    public function testEmailTemplate()
    {
        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var User $user */
        $user = $this->fixtures->getReference('user1');

        $em->refresh($user);
        $this->loginAs($user, 'main');

        $client = $this->makeClient();

        $crawler = $client->request('GET', '/admin/emailtemplates');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $emailTemplate = $em->getRepository(EmailTemplate::class)->findOneBy([]);

        $this->assertEquals("You got invited by {{senderName}}. ", $emailTemplate->getEmailSubject());
        $this->assertEquals("<br/> Here is your link {{link}} <br/><br/>", $emailTemplate->getEmailBody());
        $this->assertEquals("INVITATION", $emailTemplate->getEmailType());

        $form = $crawler->selectButton('Change Template')->form();

        $form->get('email_template')['emailSubject']->setValue("You got invited by {{senderName}}!!! ");
        $form->get('email_template')['emailBody']->setValue("<br/> Here is your link {{link}}!!!<br/><br/>");

        $client->submit($form);

        $em->refresh($emailTemplate);

        $this->assertEquals("You got invited by {{senderName}}!!!", $emailTemplate->getEmailSubject());
        $this->assertEquals("<br/> Here is your link {{link}}!!!<br/><br/>", $emailTemplate->getEmailBody());
        $this->assertEquals("INVITATION", $emailTemplate->getEmailType());
    }
}
