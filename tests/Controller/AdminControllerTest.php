<?php


namespace App\Tests\Controller;

use App\Entity\Configuration;
use App\Entity\EmailTemplate;
use App\Entity\File;
use App\Entity\Gallery;
use App\Entity\Invitation;
use App\Entity\InvitationBlacklist;
use App\Entity\User;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\ORM\EntityManager;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Field\FileFormField;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AdminControllerTest extends WebTestCase
{
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
            "App\DataFixtures\ORM\LoadEmailTemplates",
            "App\DataFixtures\ORM\LoadBlackListEmails",
            "App\DataFixtures\ORM\LoadInvitations"
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
     *  - Send invitation but with invalid email address.
     *  - Expected to get error message that email is invalid.
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

        $this->assertEquals(6, sizeof($invitations));

        $client->submit($form);

        $invitations = $invitationRepository->findAll();

        $this->assertEquals(7, sizeof($invitations));

        $mailCollector = $client->getProfile()->getCollector('swiftmailer');

        $this->assertSame(1, $mailCollector->getMessageCount());

        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];

        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertSame('You got invited by Connor Vaughan. ', $message->getSubject());
        $this->assertSame("noreply@plumtreesystems.com", key($message->getFrom()));
        $this->assertSame('myemail@gmail.com', key($message->getTo()));


        $crawler = $client->request('GET', '/associate/invite');

        $form = $crawler->selectButton('Send')->form();

        $form->get('invitation')['email']->setValue('myemaifa');
        $form->get('invitation')['fullName']->setValue('myemail');

        $crawler = $client->submit($form);

        $this->assertContains(
            'Invalid email',
            $crawler->filter('div.error__block')->html()
        );

        $crawler = $client->request('GET', '/associate/invite');

        $form = $crawler->selectButton('Send')->form();

        $form->get('invitation')['email']->setValue('AidanNewman@dayrep.com');
        $form->get('invitation')['fullName']->setValue('myemail');

        $crawler = $client->submit($form);

        $this->assertContains(
            'Associate already exists',
            $crawler->filter('div.error__block')->html()
        );

        $crawler = $client->request('GET', '/associate/invite');

        $form = $crawler->selectButton('Send')->form();

        /** @var InvitationBlacklist $invitationBlackList */
        $invitationBlackList = $this->fixtures->getReference('invitationBlackListEmail');
        $em->refresh($invitationBlackList);

        $form->get('invitation')['email']->setValue($invitationBlackList->getEmail());
        $form->get('invitation')['fullName']->setValue('myemail');



        $crawler = $client->submit($form);

        $this->assertContains(
            'The person with this email has opted out of this service',
            $crawler->filter('div.error__block')->html()
        );
    }


    /**
     *  Testing resend invitation functionality.
     *
     *  - Login as associate, get invitation fixture and call to 'associate/invite' api with a param of invitation id
     *  - Expected successfully to be sent invite mail to fixture invitation email.
     *
     *  - This time call to 'associate/invite' api with not existing invitation id.
     *  - Expected to get not found error.
     */
    public function testResendInvitation()
    {
        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var User $user */
        $user = $this->fixtures->getReference('user4');

        $em->refresh($user);
        $this->loginAs($user, 'main');

        $client = $this->makeClient();

        $invitation = $this->fixtures->getReference('invitation2');

        $client->enableProfiler();

        $crawler = $client->request(
            'GET',
            '/associate/invite',
            ['invitationId' => $invitation->getId()]
        );

        $mailCollector = $client->getProfile()->getCollector('swiftmailer');

        $this->assertSame(1, $mailCollector->getMessageCount());

        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];

        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertSame('You got invited by Bailey Brookes. ', $message->getSubject());
        $this->assertSame("noreply@plumtreesystems.com", key($message->getFrom()));
        $this->assertSame('jonas@gmail.com', key($message->getTo()));

        $crawler = $client->request(
            'GET',
            '/associate/invite',
            ['invitationId' => -1000]
        );

        $this->assertEquals(500, $client->getResponse()->getStatusCode());
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
     *  - Expected to be redirected in landing page after requests to '/' and '/associate' pages and
     * expected appropriate set landing page content. Also expected not to be redirected then requested
     * to '/landingpage' because prelaunch is ended.
     *
     *  - Change entity landing content to delta object and then again request with associate to '/'
     * when prelaunch has ended.
     *  - Expected to be redirected to landing page and appropriate landing content to be shown which is
     * parsed html from delta.
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
        $form->get('end_prelaunch')['landingContent']->setValue("<h1>Prelaunch has ended!!!</h1>");

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
            ['submit' => true, '_username' => 'admin@plumtreesystems.com', '_password' => '123456789']
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

        $client->followRedirect();

        $this->assertContains(
            "<h1>Prelaunch has ended!!!</h1>",
            $client->getResponse()->getContent()
        );

        $client->request('GET', '/associate');

        $targetUrl = $client->getResponse()->isRedirect("/landingpage");

        $this->assertTrue($targetUrl);

        $client->request('GET', '/landingpage');

        $targetUrl = $client->getResponse()->isRedirection();

        $this->assertFalse($targetUrl);

        $configurationEntity = $em->getRepository(Configuration::class)->findOneBy([]);

        $configurationEntity->setLandingContent('{"ops":[{"attributes":{"underline":true,"script":"sub"}'
            .',"insert":"hello "},'.
            '{"attributes":{"underline":true,"strike":true,"script":"sub"},"insert":"mjiujiunu"},'
            .'{"attributes":{"header":1},"insert":"\n"},{"insert":"\n"}]}');

        $em->persist($configurationEntity);
        $em->flush();

        $client->request('GET', '/');

        $client->getResponse()->isRedirect("/landingpage");

        $client->followRedirect();


        $this->assertContains(
            "<h1><u><sub>hello </sub></u><u><s><sub>mjiujiunu</sub></s></u></h1>
<p>
<br />
</p>",
            $client->getResponse()->getContent()
        );
    }

    /**
     *  Testing email template form functionality
     *
     * - Request to email templates list page when logged in as admin and click on invitation email template.
     * - Expected to find one invitation emailTemplate with appropriate params. Change form values and submit.
     * Expected that invitation emailTemplate values have updated in database.
     *
     * - Request to email templates list page when logged in as admin and click on invitation email template.
     * - Change form values to empty strings and submit.
     * - Expected error to pop up that values are empty.
     *
     * - Request to email templates list page when logged in as admin and click on reset password email template.
     * - Expected to find one reset password emailTemplate with appropriate params. Change form values and submit.
     * Expected that reset password emailTemplate values have updated in database.
     *
     * - Request to email templates list page when logged in as admin and click on welcome email template.
     * - Expected to find one welcome emailTemplate with appropriate params. Change form values and submit.
     * Expected that welcome emailTemplate values have updated in database.
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

        $crawler = $client->request('GET', '/admin/emailtemplateslist');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $invitationLink = $crawler->filter('a[href="/admin/emailtemplate/invitation"]')->link();

        $crawler = $client->click($invitationLink);

        $emailTemplate = $em->getRepository(EmailTemplate::class)->findOneBy(['emailType' => 'INVITATION']);

        $this->assertEquals("You got invited by {{senderName}}. ", $emailTemplate->getEmailSubject());
        $this->assertEquals("<h3><br/> Here is your <a href='{{link}}'>link</a></h3> ".
        "<br/><br/>To opt out of".
        " this service click <a href='{{ optOutUrl }}'>this</a> link", $emailTemplate->getEmailBody());
        $this->assertEquals("INVITATION", $emailTemplate->getEmailType());

        $form = $crawler->selectButton('Change Template')->form();

        $form->get('email_template')['emailSubject']->setValue("You got invited by {{senderName}}!!! ");
        $form->get('email_template')['emailBody']->setValue("<br/> Here is your link {{link}}!!!<br/><br/>");

        $client->submit($form);

        $em->refresh($emailTemplate);

        $this->assertEquals("You got invited by {{senderName}}!!!", $emailTemplate->getEmailSubject());
        $this->assertEquals("<br/> Here is your link {{link}}!!!<br/><br/>", $emailTemplate->getEmailBody());
        $this->assertEquals("INVITATION", $emailTemplate->getEmailType());


        $crawler = $client->request('GET', '/admin/emailtemplateslist');

        $invitationLink = $crawler->filter('a[href="/admin/emailtemplate/invitation"]')->link();

        $crawler = $client->click($invitationLink);

        $emailTemplate = $em->getRepository(EmailTemplate::class)->findOneBy(['emailType' => 'INVITATION']);

        $form = $crawler->selectButton('Change Template')->form();

        $form->get('email_template')['emailSubject']->setValue("");
        $form->get('email_template')['emailBody']->setValue("");

        $crawler = $client->submit($form);

        $em->refresh($emailTemplate);

        $this->assertEquals("You got invited by {{senderName}}!!!", $emailTemplate->getEmailSubject());
        $this->assertEquals("<br/> Here is your link {{link}}!!!<br/><br/>", $emailTemplate->getEmailBody());
        $this->assertEquals("INVITATION", $emailTemplate->getEmailType());

        $this->assertContains(
            'Please do not leave empty values',
            $crawler->filter('div.error__block')->html()
        );

        $crawler = $client->request('GET', '/admin/emailtemplateslist');

        $invitationLink = $crawler->filter('a[href="/admin/emailtemplate/password"]')->link();

        $crawler = $client->click($invitationLink);

        $emailTemplate = $em->getRepository(EmailTemplate::class)
            ->findOneBy(['emailType' => 'RESET_PASSWORD']);

        $this->assertEquals("Password Reset", $emailTemplate->getEmailSubject());
        $this->assertEquals(
            'To reset your password click <a href="{{ link }}">here</a><br/><br/>',
            $emailTemplate->getEmailBody()
        );
        $this->assertEquals("RESET_PASSWORD", $emailTemplate->getEmailType());

        $form = $crawler->selectButton('Change Template')->form();

        $form->get('email_template')['emailSubject']->setValue("Password RESETTT!!!");
        $form->get('email_template')['emailBody']->setValue("To reset password click <br>");

        $client->submit($form);

        $em->refresh($emailTemplate);

        $this->assertEquals("Password RESETTT!!!", $emailTemplate->getEmailSubject());
        $this->assertEquals("To reset password click <br>", $emailTemplate->getEmailBody());
        $this->assertEquals("RESET_PASSWORD", $emailTemplate->getEmailType());

        $crawler = $client->request('GET', '/admin/emailtemplateslist');

        $invitationLink = $crawler->filter('a[href="/admin/emailtemplate/welcome"]')->link();

        $crawler = $client->click($invitationLink);

        $emailTemplate = $em->getRepository(EmailTemplate::class)
            ->findOneBy(['emailType' => 'WELCOME']);

        $this->assertEquals("Welcome", $emailTemplate->getEmailSubject());
        $this->assertEquals(
            'Hello {{ name }}, welcome to prelaunch!',
            $emailTemplate->getEmailBody()
        );
        $this->assertEquals("WELCOME", $emailTemplate->getEmailType());

        $form = $crawler->selectButton('Change Template')->form();

        $form->get('email_template')['emailSubject']->setValue("Welcome!!!");
        $form->get('email_template')['emailBody']->setValue("Welcome <a></a>guest");

        $client->submit($form);

        $em->refresh($emailTemplate);

        $this->assertEquals("Welcome!!!", $emailTemplate->getEmailSubject());
        $this->assertEquals("Welcome <a></a>guest", $emailTemplate->getEmailBody());
        $this->assertEquals("WELCOME", $emailTemplate->getEmailType());
    }

    /**
     *  Testing change content form functionality
     *
     * - Request to change content page when logged in as admin and expected to find one configuration with
     * null main logo and termsOfServices params. Upload correct files and submit.
     * - Expected that configuration values have been uploaded in database. Also expected that user can download
     * uploaded image with /download/{id} api.
     *
     * - Request to change content page when logged in as admin and expected to find one configuration with
     * appropriate params. Change form values, upload correct files and submit.
     * - Expected that configuration values have been updated in database.
     *
     * - Request to change content page when loggend in as admin. This time change hidden form values, upload
     * created gallery file id's and submit.
     * - Expected that configuration entity has been updated with new mainLogo and termsOfService values.
     *
     * - Request to change content page when logged in as admin and expected to find one configuration with
     * appropriate params. Change form values, upload incorrect files and submit.
     * - Expected to get error message that only images are allowed.
     */
    public function testChangeContent()
    {
        $this->setOutputCallback(function () {
        });

        $_SERVER['REQUEST_URI'] = "/admin/changecontent";

        $container = $this->getContainer();

        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var User $user */
        $user = $this->fixtures->getReference('user1');

        $em->refresh($user);
        $this->loginAs($user, 'main');

        $client = $this->makeClient();

        $crawler = $client->request('GET', '/admin/changecontent');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $configuration = $em->getRepository(Configuration::class)->findOneBy([]);

        $this->assertEquals(null, $configuration->getMainLogo());
        $this->assertEquals(null, $configuration->getTermsOfServices());
        $this->assertEquals(null, $configuration->getTosDisclaimer());

        $path = $client->getContainer()->getParameter('kernel.project_dir').'/var/test_files';

        $form = $crawler->selectButton('Change content')->form();

        /** @var FileFormField $fileInput */
        $fileInput = $form->get('change_content')['mainLogo'];
        $fileInput->upload($path.'/test.png');

        $fileInput = $form->get('change_content')['termsOfServices'];
        $fileInput->upload($path.'/test2.png');

        $files = $form->getPhpFiles();
        $files['change_content']['mainLogo']['type'] = 'image/jpeg';
        $files['change_content']['termsOfServices']['type'] = 'image/jpeg';
        $csrf_protection = $form['change_content']['_token'];

        $client->request(
            'POST',
            '/admin/changecontent',
            [
                'change_content' => [
                    'tosDisclaimer' => 'disclaimer',
                    '_token' => $csrf_protection->getValue(),
                    'Submit' => true
                ]
            ],
            $files
        );

        $em->refresh($configuration);

        $this->assertNotNull($configuration->getTermsOfServices());
        $this->assertNotNull($configuration->getMainLogo());
        $this->assertEquals('disclaimer', $configuration->getTosDisclaimer());

        $client->request('HEAD', '/download/1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals(
            'attachment; filename="test.png";',
            $client->getResponse()->headers->all()['content-disposition']['0']
        );

        $crawler = $client->request('GET', '/admin/changecontent');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $configuration = $em->getRepository(Configuration::class)->findOneBy([]);

        $this->assertNotNull($configuration->getTermsOfServices());
        $this->assertNotNull($configuration->getMainLogo());

        $path = $client->getContainer()->getParameter('kernel.project_dir').'/var/test_files';

        $form = $crawler->selectButton('Change content')->form();

        /** @var FileFormField $fileInput */
        $fileInput = $form->get('change_content')['mainLogo'];
        $fileInput->upload($path.'/test.png');

        $fileInput = $form->get('change_content')['termsOfServices'];
        $fileInput->upload($path.'/test2.png');

        $files = $form->getPhpFiles();
        $files['change_content']['mainLogo']['type'] = 'image/jpeg';
        $files['change_content']['termsOfServices']['type'] = 'image/jpeg';
        $csrf_protection = $form['change_content']['_token'];

        $client->request(
            'POST',
            '/admin/changecontent',
            [
                'change_content' => ['_token' => $csrf_protection->getValue(), 'Submit' => true]
            ],
            $files
        );

        $em->refresh($configuration);

        $this->assertNotNull($configuration->getTermsOfServices());
        $this->assertNotNull($configuration->getMainLogo());

        $crawler = $client->request('GET', '/admin/changecontent');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $configuration = $em->getRepository(Configuration::class)->findOneBy([]);

        $this->assertNotNull($configuration->getTermsOfServices());
        $this->assertNotNull($configuration->getMainLogo());

        $path = $client->getContainer()->getParameter('kernel.project_dir').'/var/test_files';

        $form = $crawler->selectButton('Change content')->form();

        /** @var FileFormField $fileInput */
        $fileInput = $form->get('change_content')['mainLogo'];
        $fileInput->upload($path.'/test.png');

        $files = $form->getPhpFiles();
        $files['change_content']['mainLogo']['type'] = 'image/jpeg';
        $csrf_protection = $form['change_content']['_token'];

        $client->request(
            'POST',
            '/admin/changecontent',
            [
                'change_content' => ['_token' => $csrf_protection->getValue(), 'Submit' => true]
            ],
            $files
        );

        $em->refresh($configuration);

        $this->assertNotNull($configuration->getTermsOfServices());
        $this->assertNotNull($configuration->getMainLogo());

        $crawler = $client->request('GET', '/admin/changecontent');

        $configuration = $em->getRepository(Configuration::class)->findOneBy([]);

        $path = $client->getContainer()->getParameter('kernel.project_dir').'/var/test_files/test.png';

        $form = $crawler->selectButton('Change content')->form();

        $ptsFile = new File();

        $image  = new UploadedFile(
            $path,
            'test.png',
            'image/jpeg',
            null
        );

        $ptsFile->setUploadedFileReference($image);
        $ptsFile->setOriginalName('test.png');
        $ptsFile->setName('test.png');

        $galleryFile = new Gallery();
        $galleryFile->setGalleryFile($ptsFile);
        $galleryFile->setMimeType('image/jpeg');

        $em->persist($galleryFile);
        $em->flush();

        $id = $galleryFile->getId();

        $form['change_content[hiddenMainLogoFile]'] = $id;
        $form['change_content[hiddenTermsOfServiceFile]'] = $id;

        $client->submit($form);

        $em->refresh($configuration);

        $this->assertEquals(
            'test.png',
            $configuration->getMainLogo()->getUploadedFileReference()->getClientOriginalName()
        );
        $this->assertEquals(
            'test.png',
            $configuration->getTermsOfServices()->getUploadedFileReference()->getClientOriginalName()
        );

        $crawler = $client->request('GET', '/admin/changecontent');

        $configuration = $em->getRepository(Configuration::class)->findOneBy([]);

        $this->assertNotNull($configuration->getTermsOfServices());
        $this->assertNotNull($configuration->getMainLogo());

        $path = $client->getContainer()->getParameter('kernel.project_dir').'/var/test_files';

        $form = $crawler->selectButton('Change content')->form();

        /** @var FileFormField $fileInput */
        $fileInput = $form->get('change_content')['mainLogo'];
        $fileInput->upload($path.'/notimage.txt');

        $fileInput = $form->get('change_content')['termsOfServices'];
        $fileInput->upload($path.'/notimage.txt');

        $files = $form->getPhpFiles();
        $files['change_content']['mainLogo']['type'] = 'text/html';
        $files['change_content']['termsOfServices']['type'] = 'text/html';
        $csrf_protection = $form['change_content']['_token'];

        $crawler = $client->request(
            'POST',
            '/admin/changecontent',
            [
                'change_content' => ['_token' => $csrf_protection->getValue(), 'Submit' => true]
            ],
            $files
        );

        $this->assertContains(
            'Only images are allowed',
            $crawler->filter('div.error__block')->html()
        );

        $crawler = $client->request('GET', '/admin/changecontent');

        $configuration = $em->getRepository(Configuration::class)->findOneBy([]);

        $this->assertNotNull($configuration->getTermsOfServices());
        $this->assertNotNull($configuration->getMainLogo());

        $form = $crawler->selectButton('Change content')->form();

        /** @var FileFormField $fileInput */
        $fileInput = $form->get('change_content')['mainLogo'];
        $fileInput->upload($path.'/notfile');

        $fileInput = $form->get('change_content')['termsOfServices'];
        $fileInput->upload($path.'/notfile');

        $files = $form->getPhpFiles();
        $files['change_content']['mainLogo']['type'] = 'text/html';
        $files['change_content']['termsOfServices']['type'] = 'text/html';
        $csrf_protection = $form['change_content']['_token'];

        $client->request(
            'POST',
            '/admin/changecontent',
            [
                'change_content' => ['_token' => $csrf_protection->getValue(), 'Submit' => true]
            ],
            $files
        );

        $gaufretteFilteManager = $container->get('pts_file.manager');

        $em = $container->get('doctrine.orm.default_entity_manager');

        $fileObj = $em->getRepository(\App\Entity\File::class);

        $allFiles = $fileObj->findAll();

        foreach ($allFiles as $file) {
            $gaufretteFilteManager->remove($file);
        }
    }

    /**
     *  Testing getCompanyRoot controller if it returns correct json response
     *
     *  - Request to /admin/api/explorer.
     *  - Expect to get json response about company
     *
     *  - Request to /admin/api/explorer with parameter id of 3
     *  - Expected to get 2 associates which has parent id 3 and appropriate values of json response.
     */
    public function testGetCompanyRoot()
    {
        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var User $user */
        $user = $this->fixtures->getReference('user1');

        $em->refresh($user);
        $this->loginAs($user, 'main');

        $client = $this->makeClient();

        $client->request('GET', '/admin/api/explorer');

        $jsonResponse = $client->getResponse()->getContent();

        $responseArr = json_decode($jsonResponse, true);

        $this->assertEquals(4, sizeof($responseArr));

        $this->assertEquals('-1', $responseArr['id']);
        $this->assertEquals("Company", $responseArr['title']);
        $this->assertEquals('-2', $responseArr['parentId']);
        $this->assertEquals('2', $responseArr['numberOfChildren']);

        $client->request('GET', '/admin/api/explorer', ['id' => '3']);

        $jsonResponse = $client->getResponse()->getContent();

        $responseArr = json_decode($jsonResponse, true);

        $user6 = $em->getRepository(User::class)->find(6);
        $user7 = $em->getRepository(User::class)->find(7);

        $this->assertEquals(2, sizeof($responseArr));

        $usersArray = [
            [
                'id' => $user6->getId(),
                'title' => $user6->getAssociate()->getFullName(),
                'parentId' => $user6->getAssociate()->getParentId(),
                'numberOfChildren' => '1'
            ],
            [
                'id' => $user7->getId(),
                'title' => $user7->getAssociate()->getFullName(),
                'parentId' => $user7->getAssociate()->getParentId(),
                'numberOfChildren' => '3'
            ]
        ];

        $this->assertContains(
            $usersArray[0],
            $responseArr
        );

        $this->assertContains(
            $usersArray[1],
            $responseArr
        );
    }

    /**
     *  Testing admin main page
     *
     *  - Request to '/' main page and logged in as admin.
     *  - Expected to get redirection status code and then redirected to admin main page. Also expected to get
     * appropriate number of levelBarListItem in main admin page and appropriate number of sidebar items in menu.
     */
    public function testAdminMainPage()
    {
        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var User $user */
        $user = $this->fixtures->getReference('user1');

        $em->refresh($user);
        $this->loginAs($user, 'main');

        $client = $this->makeClient();

        $client->request('GET', '/');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $crawler = $client->followRedirect();

        $this->assertEquals('/admin', $client->getRequest()->getRequestUri());

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals(
            5,
            $crawler->filter('li.associate-levelBarListItem')->count()
        );

        $this->assertEquals(
            14,
            $crawler->filter('div.sidebar-item')->count()
        );
    }

    /**
     *  Testing user search admin api if admin can go to /admin/usersearch link and have usersearch in it.
     */
    public function testUserSearch()
    {
        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var User $user */
        $user = $this->fixtures->getReference('user1');

        $em->refresh($user);
        $this->loginAs($user, 'main');

        $client = $this->makeClient();

        $crawler = $client->request('GET', '/admin/usersearch');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals(
            1,
            $crawler->filter('span.card-title:contains("User search")')->count()
        );
    }

    /**
     *  Testing downloadable csv file
     *
     *  - Request to /admin/csv api.
     *  - Expected to get headers which states that it returns attachment with a filename and it can be downloadable.
     */
    public function testExportToCsv()
    {
        $this->setOutputCallback(function () {
        });

        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var User $user */
        $user = $this->fixtures->getReference('user1');

        $em->refresh($user);
        $this->loginAs($user, 'main');

        $client = $this->makeClient();

        $client->request('HEAD', '/admin/csv');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals(
            "attachment; filename=associates.csv",
            $client->getResponse()->headers->all()['content-disposition']['0']
        );
    }

    /**
     *  Testing /associate/info api as admin logged in whether it returns empty response.
     */
    public function testGetBrokenAssociateWithAdmin()
    {
        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var User $user */
        $user = $this->fixtures->getReference('user1');

        $em->refresh($user);
        $this->loginAs($user, 'main');

        $client = $this->makeClient();

        $client->request('GET', '/associate/info');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals(
            '',
            $client->getResponse()->getContent()
        );
    }

    /**
     *  Testing uploadFile api whether it returns expected response
     *
     *  - Request to /admin/uploadFile api with GET method and expect to get empty response content.
     *
     *  - Request to /admin/uploadFile api with POST method with additional created pts file download path param.
     *  - Expected to get 200 status code and http://localhost/download/ partial string to be returned for downloading
     * images.
     */
    public function testUploadEditorImage()
    {
        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var User $user */
        $user = $this->fixtures->getReference('user1');

        $em->refresh($user);
        $this->loginAs($user, 'main');

        $client = $this->makeClient();

        $container = $client->getContainer();

        $client->request('GET', '/admin/uploadFile');

        $this->assertEquals('', $client->getResponse()->getContent());

        $path = $client->getContainer()->getParameter('kernel.project_dir').'/var/test_files/test.png';

        $gaufretteFilteManager = $container->get('pts_file.manager');

        $ptsFile = new File();

        $image  = new UploadedFile(
            $path,
            'test.png',
            'image/jpeg',
            null
        );

        $ptsFile->setUploadedFileReference($image);
        $ptsFile->setOriginalName('test.png');
        $ptsFile->setName('test.png');

        $em->persist($ptsFile);
        $em->flush();

        $filePath = $gaufretteFilteManager->generateDownloadUrl($ptsFile);

        $client->request(
            'POST',
            '/admin/uploadFile',
            [],
            [],
            [],
            $filePath
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $responseContent = json_decode($client->getResponse()->getContent());

        $this->assertContains(
            'http://localhost/download',
            $responseContent
        );

        $em = $container->get('doctrine.orm.default_entity_manager');

        $fileObj = $em->getRepository(\App\Entity\File::class);

        $allFiles = $fileObj->findAll();

        foreach ($allFiles as $file) {
            $gaufretteFilteManager->remove($file);
        }
    }

    /**
     *  Testing /admin/uploadGalleryFile api whether it returns serialized file.
     *
     *  - Request to /admin/uploadGalleryFile api with GET method and expect to get empty response content.
     *
     *  - Request to /admin/uploadGalleryFile api with POST method with additional file param.
     *  - Expected to get 200 status code and serialized appropriate gallery file to be returned.
     */
    public function testUploadGalleryFile()
    {
        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var User $user */
        $user = $this->fixtures->getReference('user1');

        $em->refresh($user);
        $this->loginAs($user, 'main');

        $client = $this->makeClient();

        $container = $client->getContainer();

        $client->request('GET', '/admin/uploadGalleryFile');

        $this->assertEquals('', $client->getResponse()->getContent());

        $path = $client->getContainer()->getParameter('kernel.project_dir').'/var/test_files/test.png';

        $image = new UploadedFile(
            $path,
            'test.png',
            'image/png',
            null
        );

        $client->request(
            'POST',
            '/admin/uploadGalleryFile',
            [],
            ['galleryFile' => $image]
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $responseContent = json_decode($client->getResponse()->getContent(), true);

        $responseFile = $responseContent['file'];

        $this->assertArrayHasKey('id', $responseFile);
        $this->assertArrayHasKey('galleryFile', $responseFile);
        $this->assertArrayHasKey('filePath', $responseFile);
        $this->assertArrayHasKey('created', $responseFile);
        $this->assertEquals('image/png', $responseFile['mimeType']);

        $gaufretteFilteManager = $container->get('pts_file.manager');

        $em = $container->get('doctrine.orm.default_entity_manager');

        $fileObj = $em->getRepository(\App\Entity\File::class);

        $allFiles = $fileObj->findAll();

        foreach ($allFiles as $file) {
            $gaufretteFilteManager->remove($file);
        }
    }
}
