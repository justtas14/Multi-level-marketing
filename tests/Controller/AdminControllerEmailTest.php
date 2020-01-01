<?php

namespace App\Tests\Controller;

use App\Entity\EmailTemplate;
use App\Entity\User;
use App\Tests\Reusables\LoginOperations;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\ORM\EntityManager;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class AdminControllerEmailTest extends WebTestCase
{
    use LoginOperations;
    /**
     * @var ReferenceRepository
     */
    private $fixtures;

    /**
     * @group legacy
     */
    protected function setUp() : void
    {
        parent::setUp();
        $this->fixtures = $this->loadFixtures([
            "App\DataFixtures\ORM\LoadUsers",
        ])->getReferenceRepository();
    }

    /**
     *  Testing fetcth email template service if it creates new default appropriate email template if it doesnt exist.
     * Also testing api appropriate json response.
     *
     *  - Request to email template with a slug parameter invitation.
     *  - Expected to have new default invitation email template to be created in database. Also expected for api
     * to return appropriate json response.
     *
     *  - Request to email template with a slug parameter welcome.
     *  - Expected to have new default welcome email template to be created in database. Also expected for api to return
     * appropriate json response.
     *
     *  - Request to email template with a wrong slug parameter.
     *  - Expected to have error and 404 not found exception to be thrown. Also expected for api to return appropriate
     * json response.
     *
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

        $jwtManager = $client->getContainer()->get('pts_user.jwt.manager');

        $token = $this->getToken($jwtManager, $user);

        $client->xmlHttpRequest(
            'POST',
            '/api/admin/emailtemplate/invitation',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

        $jsonResponse = $client->getResponse()->getContent();

        $responseArr = json_decode($jsonResponse, true);

        $emailTemplate = $em->getRepository(EmailTemplate::class)->findOneBy(['emailType' => 'INVITATION']);

        $this->assertEquals("You got invited by {{senderName}}. ", $emailTemplate->getEmailSubject());
        $this->assertEquals("<br/> Here is your <a href='{{link}}'>link</a> ".
        "<br/><br/>To opt out of this service click ".
        "<a href='{{ optOutUrl }}'>this</a> link", $emailTemplate->getEmailBody());
        $this->assertEquals("INVITATION", $emailTemplate->getEmailType());

        $this->assertEquals(false, $responseArr['formSuccess']);
        $this->assertEquals("", $responseArr['formError']);
        $this->assertEquals($emailTemplate->getEmailSubject(), $responseArr['emailTemplate']['emailSubject']);
        $this->assertEquals($emailTemplate->getEmailBody(), $responseArr['emailTemplate']['emailBody']);
        $this->assertEquals('Invitation email template', $responseArr['title']);
        $this->assertEquals('{{ senderName }}', $responseArr['availableParameters']['Sender name']);
        $this->assertEquals('{{ receiverName }}', $responseArr['availableParameters']['Receiver name']);
        $this->assertEquals('{{ link }}', $responseArr['availableParameters']['Invitation link']);
        $this->assertEquals('{{ optOutUrl }}', $responseArr['availableParameters']['Opt out of service link']);

        $client->xmlHttpRequest(
            'POST',
            '/api/admin/emailtemplate/password',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

        $jsonResponse = $client->getResponse()->getContent();

        $responseArr = json_decode($jsonResponse, true);

        $emailTemplate = $em->getRepository(EmailTemplate::class)
            ->findOneBy(['emailType' => 'RESET_PASSWORD']);

        $this->assertEquals("Password Reset", $emailTemplate->getEmailSubject());
        $this->assertEquals(
            'To reset your password click <a href="{{ link }}">here</a><br/><br/>',
            $emailTemplate->getEmailBody()
        );
        $this->assertEquals("RESET_PASSWORD", $emailTemplate->getEmailType());

        $this->assertEquals(false, $responseArr['formSuccess']);
        $this->assertEquals("", $responseArr['formError']);
        $this->assertEquals($emailTemplate->getEmailSubject(), $responseArr['emailTemplate']['emailSubject']);
        $this->assertEquals($emailTemplate->getEmailBody(), $responseArr['emailTemplate']['emailBody']);
        $this->assertEquals('Reset password email template', $responseArr['title']);
        $this->assertEquals('{{ link }}', $responseArr['availableParameters']['Reset password link']);

        $client->xmlHttpRequest(
            'POST',
            '/api/admin/emailtemplate/welcome',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

        $jsonResponse = $client->getResponse()->getContent();

        $responseArr = json_decode($jsonResponse, true);

        $emailTemplate = $em->getRepository(EmailTemplate::class)
            ->findOneBy(['emailType' => 'WELCOME']);

        $this->assertEquals("Welcome", $emailTemplate->getEmailSubject());
        $this->assertEquals(
            'Hello {{ name }}, welcome to prelaunch!',
            $emailTemplate->getEmailBody()
        );
        $this->assertEquals("WELCOME", $emailTemplate->getEmailType());

        $this->assertEquals(false, $responseArr['formSuccess']);
        $this->assertEquals("", $responseArr['formError']);
        $this->assertEquals($emailTemplate->getEmailSubject(), $responseArr['emailTemplate']['emailSubject']);
        $this->assertEquals($emailTemplate->getEmailBody(), $responseArr['emailTemplate']['emailBody']);
        $this->assertEquals('Welcome email template', $responseArr['title']);
        $this->assertEquals('{{ name }}', $responseArr['availableParameters']['Full user name']);

        $client->xmlHttpRequest(
            'POST',
            '/api/admin/emailtemplate/wrong',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}
