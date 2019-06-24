<?php


namespace App\Tests\Controller;

use App\Entity\Configuration;
use App\Entity\EmailTemplate;
use App\Entity\Invitation;
use App\Entity\User;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\ORM\EntityManager;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Field\FileFormField;

class HomeControllerTest extends WebTestCase
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
            "App\DataFixtures\ORM\LoadInvitations",
            "App\DataFixtures\ORM\LoadEmailTemplates"
        ])->getReferenceRepository();
    }

    /**
     *  Testing user registration after he gets invitation
     *
     *  - Get invitation2 entity and request to /register/{invitationCode} with appropriate.
     * invitation code of invitation entity. Expected registration form to open.
     *  - Set all correct values in form.
     *  - Expected to get successful registration message and appropriate user and associate to be added in database.
     * with inputed fields. Also expected that new user data is correctly converted to array which is required for csv.
     *
     *  - Get invitation1 entity and request to /register/{invitationCode} with appropriate.
     * invitation code of invitation entity.
     *  - Expected page with content 'The link is either expired or already used'.
     * because invitation1 entity is already used.
     *
     *  - Get invitation3 entity and request to /register/{invitationCode} with appropriate.
     * invitation code of invitation entity.
     *  - Expected page with content 'The link is either expired or already used'.
     * because invitation1 entity is expired.
     *
     *  - Get invitation5 entity and request to /register/{invitationCode} with appropriate.
     * invitation code of invitation entity. Expected registration form to open.
     *  - Set all correct values in form expect email which is already taken.
     *  - Expected to get error flash message that inputed email is already exist.
     *
     *  - Get invitation6 entity and request to /register/{invitationCode} with appropriate.
     * invitation code of invitation entity. Expected registration form to open.
     *  - Set all correct values in form expect not equal repeated password.
     *  - Expected to get error that password fields dont match.
     *
     *  - Get invitation6 entity and request to /register/{invitationCode} with appropriate.
     * invitation code of invitation entity. Expected registration form to open.
     *  - Set all correct values in form expect empty repeated passwords.
     *  - Expected to get error that password fields is empty.
     */
    public function testRegisterAssociate()
    {
        $container = $this->getContainer();

        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var Invitation $invitation */
        $invitation = $this->fixtures->getReference('invitation2');

        $em->refresh($invitation);

        $client = $this->makeClient();

        $crawler = $client->request('GET', '/register/'.$invitation->getInvitationCode());

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Save')->form();

        $form->get('user_registration')['email']->setValue($invitation->getEmail());
        $form->get('user_registration')['plainPassword']['first']->setValue('justtas');
        $form->get('user_registration')['plainPassword']['second']->setValue('justtas');
        $form->get('user_registration')['associate']['fullName']->setValue($invitation->getFullName());
        $form->get('user_registration')['associate']['dateOfBirth']['day']->setValue('20');
        $form->get('user_registration')['associate']['dateOfBirth']['month']->setValue('4');
        $form->get('user_registration')['associate']['dateOfBirth']['year']->setValue('1994');
        $form->get('user_registration')['associate']['country']->setValue('LT');
        $form->get('user_registration')['associate']['address']->setValue('blaha');
        $form->get('user_registration')['associate']['address2']->setValue('blaha');
        $form->get('user_registration')['associate']['city']->setValue('kretinga');
        $form->get('user_registration')['associate']['postcode']->setValue('12345');
        $form->get('user_registration')['associate']['mobilePhone']->setValue('86757');
        $form->get('user_registration')['associate']['homePhone']->setValue('23543');
        $form->get('user_registration')['associate']['agreedToEmailUpdates']->setValue(1);
        $form->get('user_registration')['associate']['agreedToTextMessageUpdates']->setValue(1);
        $form->get('user_registration')['associate']['agreedToTermsOfService']->setValue(1);
        $form->get('user_registration')['associate']['profilePicture']->setValue(null);

        $client->submit($form);

        $userRepository = $em->getRepository(User::class);

        $addedUser = $userRepository->findOneBy(['email' => $invitation->getEmail()]);

        $encoder = $container->get('security.user_password_encoder.generic');
        $this->assertTrue($encoder->isPasswordValid($addedUser, 'justtas'));

        $this->assertEquals($invitation->getFullName(), $addedUser->getAssociate()->getFullName());
        $this->assertEquals(
            "1994-04-20",
            date_format($addedUser->getAssociate()->getDateOfBirth(), "Y-m-d")
        );
        $this->assertEquals('LT', $addedUser->getAssociate()->getCountry());
        $this->assertEquals('blaha', $addedUser->getAssociate()->getAddress());
        $this->assertEquals('kretinga', $addedUser->getAssociate()->getCity());
        $this->assertEquals('12345', $addedUser->getAssociate()->getPostcode());
        $this->assertEquals('86757', $addedUser->getAssociate()->getMobilePhone());
        $this->assertEquals('23543', $addedUser->getAssociate()->getHomePhone());
        $this->assertEquals(true, $addedUser->getAssociate()->isAgreedToEmailUpdates());
        $this->assertEquals(true, $addedUser->getAssociate()->isAgreedToTextMessageUpdates());
        $this->assertEquals(true, $addedUser->getAssociate()->getAgreedToTermsOfService());
        $this->assertEquals(null, $addedUser->getAssociate()->getProfilePicture());

        $csvAssociateExample = $addedUser->getAssociate()->toArray();

        $this->assertEquals(18, sizeof($csvAssociateExample));

        $this->assertEquals('jonas@gmail.com', $csvAssociateExample['email']);
        $this->assertEquals('Lithuania', $csvAssociateExample['country']);
        $this->assertEquals('blaha', $csvAssociateExample['address2']);
        $this->assertEquals('1994-04-20', $csvAssociateExample['dateOfBirth']);


        /** @var Invitation $invitation */
        $invitation = $this->fixtures->getReference('invitation1');

        $em->refresh($invitation);

        $client = $this->makeClient();

        $client->request('GET', '/register/'.$invitation->getInvitationCode());

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertContains(
            'The link is either expired or already used',
            $client->getResponse()->getContent()
        );

        /** @var Invitation $invitation */
        $invitation = $this->fixtures->getReference('invitation3');

        $em->refresh($invitation);

        $client = $this->makeClient();

        $client->request('GET', '/register/'.$invitation->getInvitationCode());

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertContains(
            'The link is either expired or already used',
            $client->getResponse()->getContent()
        );

        /** @var Invitation $invitation */
        $invitation = $this->fixtures->getReference('invitation5');

        $em->refresh($invitation);

        $client = $this->makeClient();

        $crawler = $client->request('GET', '/register/'.$invitation->getInvitationCode());

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Save')->form();

        $form->get('user_registration')['email']->setValue($invitation->getEmail());
        $form->get('user_registration')['plainPassword']['first']->setValue('justtas');
        $form->get('user_registration')['plainPassword']['second']->setValue('justtas');
        $form->get('user_registration')['associate']['fullName']->setValue($invitation->getFullName());
        $form->get('user_registration')['associate']['dateOfBirth']['day']->setValue(null);
        $form->get('user_registration')['associate']['dateOfBirth']['month']->setValue(null);
        $form->get('user_registration')['associate']['dateOfBirth']['year']->setValue(null);
        $form->get('user_registration')['associate']['country']->setValue('LT');
        $form->get('user_registration')['associate']['address']->setValue('blaha');
        $form->get('user_registration')['associate']['city']->setValue('kretinga');
        $form->get('user_registration')['associate']['postcode']->setValue('12345');
        $form->get('user_registration')['associate']['mobilePhone']->setValue('86757');
        $form->get('user_registration')['associate']['homePhone']->setValue('23543');
        $form->get('user_registration')['associate']['agreedToEmailUpdates']->setValue(1);
        $form->get('user_registration')['associate']['agreedToTextMessageUpdates']->setValue(1);
        $form->get('user_registration')['associate']['agreedToTermsOfService']->setValue(1);
        $form->get('user_registration')['associate']['profilePicture']->setValue(null);

        $crawler = $client->submit($form);

        $this->assertContains(
            'Date of birth is required',
            $crawler->filter('div.error__block')->html()
        );

        /** @var Invitation $invitation */
        $invitation = $this->fixtures->getReference('invitation6');

        $em->refresh($invitation);

        $client = $this->makeClient();

        $crawler = $client->request('GET', '/register/'.$invitation->getInvitationCode());

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Save')->form();

        $form->get('user_registration')['email']->setValue($invitation->getEmail());
        $form->get('user_registration')['plainPassword']['first']->setValue('justtas');
        $form->get('user_registration')['plainPassword']['second']->setValue('justt');
        $form->get('user_registration')['associate']['fullName']->setValue($invitation->getFullName());
        $form->get('user_registration')['associate']['country']->setValue('LT');
        $form->get('user_registration')['associate']['address']->setValue('blaha');
        $form->get('user_registration')['associate']['city']->setValue('kretinga');
        $form->get('user_registration')['associate']['postcode']->setValue('12345');
        $form->get('user_registration')['associate']['mobilePhone']->setValue('86757');
        $form->get('user_registration')['associate']['homePhone']->setValue('23543');
        $form->get('user_registration')['associate']['agreedToEmailUpdates']->setValue(1);
        $form->get('user_registration')['associate']['agreedToTextMessageUpdates']->setValue(1);
        $form->get('user_registration')['associate']['agreedToTermsOfService']->setValue(1);
        $form->get('user_registration')['associate']['profilePicture']->setValue(null);

        $client->submit($form);

        $this->assertContains(
            'The password fields must match.',
            $client->getResponse()->getContent()
        );

        /** @var Invitation $invitation */
        $invitation = $this->fixtures->getReference('invitation6');

        $em->refresh($invitation);

        $client = $this->makeClient();

        $crawler = $client->request('GET', '/register/'.$invitation->getInvitationCode());

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Save')->form();

        $form->get('user_registration')['email']->setValue($invitation->getEmail());
        $form->get('user_registration')['plainPassword']['first']->setValue('');
        $form->get('user_registration')['plainPassword']['second']->setValue('');
        $form->get('user_registration')['associate']['fullName']->setValue($invitation->getFullName());
        $form->get('user_registration')['associate']['country']->setValue('LT');
        $form->get('user_registration')['associate']['address']->setValue('blaha');
        $form->get('user_registration')['associate']['city']->setValue('kretinga');
        $form->get('user_registration')['associate']['postcode']->setValue('12345');
        $form->get('user_registration')['associate']['mobilePhone']->setValue('86757');
        $form->get('user_registration')['associate']['homePhone']->setValue('23543');
        $form->get('user_registration')['associate']['agreedToEmailUpdates']->setValue(1);
        $form->get('user_registration')['associate']['agreedToTextMessageUpdates']->setValue(1);
        $form->get('user_registration')['associate']['agreedToTermsOfService']->setValue(1);
        $form->get('user_registration')['associate']['profilePicture']->setValue(null);

        $client->submit($form);

        $this->assertContains(
            'This value should not be blank.',
            $client->getResponse()->getContent()
        );
    }

    /**
     *  Testing main page redirection when not logged in
     */
    public function testRedirectToLogin()
    {
        $client = $this->makeClient();

        $client->request('GET', '/');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->followRedirect();

        $this->assertEquals('/login', $client->getRequest()->getRequestUri());

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     *  Testing opt out controller functionality
     *
     *  - Request to /optOut controller with a slug of invitation code of invitation with id 2.
     *  - Expected to go to the page and success message to be appeared that user is opt out of the service.
     *
     *  - Request to /optOut controller with a slug of invitation code of invitation with id 2.
     *  - Expected to go to the page and message to be appeared that user has already been opt out of the service.
     *
     *  - Request to /optOut controller with a slug of wrong invitation code.
     *  - Expected to get 404 status code and error message that invitation was not found.
     */
    public function testOptOutAction()
    {
        $client = $this->makeClient();

        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var Invitation $invitation*/
        $invitation = $this->fixtures->getReference('invitation2');

        $em->refresh($invitation);

        $client->request('GET', '/optOut/'.$invitation->getInvitationCode());

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertContains(
            'Successfully opted out of the service',
            $client->getResponse()->getContent()
        );

        $client->request('GET', '/optOut/'.$invitation->getInvitationCode());

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertContains(
            'You have already opted out of the service',
            $client->getResponse()->getContent()
        );

        $client->request('GET', '/optOut/wrongInvitation');

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    /**
     *  Testing case when wrong token is sent.
     */
    public function testWrongToken()
    {
        $client = $this->makeClient();

        $tokenStorage = $client->getContainer()->get('security.token_storage');

        $tokenStorage->setToken(null);

        $crawler = $client->request(
            'POST',
            '/login',
            ['headers' => [
                'Authorization' => $tokenStorage->getToken()
            ]]
        );

        $this->assertEquals(400, $client->getResponse()->getStatusCode());

        $this->assertContains(
            'Bad Request',
            $crawler->filter('h2.exception-http')->html()
        );
    }

    /**
     *  Testing restore password functionality
     *
     *  - Request to /restorePassword api,
     *  - Expected to go to that page. Then submit not existing email. Expected appropriate error message to appear.
     *
     *  - Submit with actual existing email.
     *  - Expect to get 1 email with an appropriate content and expected for user new reset password code.
     *
     *  - Request to /restorePassword with reset password code in slug,
     *  - Expected to go to that page. Then submit with empty password. Expected appropriate error message to appear.
     *
     *  - Submit with correct same repeated password.
     *  - Expect to be redirected to login page and to be password changed for user with entered email
     * in reset password form.
     *
     *  - Request to /restorePassword api, enter valid email, but set on user expired reset password.
     * Then go to reset password api with created reset password code in params.
     *  - Expected to go to page with a message that user is either expired or not found.
     *
     */
    public function testRestorePassword()
    {
        $container = $this->getContainer();

        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        $client = $this->makeClient();

        $crawler = $client->request('GET', '/restorePassword');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        /** @var User $user */
        $user = $this->fixtures->getReference('user2');

        $em->refresh($user);

        $form = $crawler->selectButton('Send')->form();

        $form->get('reset_password')['email']->setValue("notexist");

        $crawler = $client->submit($form);

        $this->assertContains(
            'This email doesnt exist',
            $crawler->filter('div.error__block')->html()
        );

        $form->get('reset_password')['email']->setValue($user->getEmail());

        $client->enableProfiler();

        $client->submit($form);

        $em->refresh($user);

        $mailCollector = $client->getProfile()->getCollector('swiftmailer');

        $this->assertSame(1, $mailCollector->getMessageCount());

        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];

        /** @var EmailTemplate $resetPasswordTemplate */
        $resetPasswordTemplate = $this->fixtures->getReference('emailTemplateResetPassword');

        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertSame($resetPasswordTemplate->getEmailSubject(), $message->getSubject());
        $this->assertSame("noreply@plumtreesystems.com", key($message->getFrom()));
        $this->assertSame($user->getEmail(), key($message->getTo()));

        $crawler = $client->request('GET', '/restorePassword/'.$user->getResetPasswordCode());

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Reset Password')->form();


        $form->get('new_password')['newPassword']['first']->setValue('');
        $form->get('new_password')['newPassword']['second']->setValue('');

        $crawler = $client->submit($form);

        $this->assertContains(
            'Passsword cannot be empty',
            $crawler->filter('div.error__block')->html()
        );

        $form->get('new_password')['newPassword']['first']->setValue('as');
        $form->get('new_password')['newPassword']['second']->setValue('as');

        $client->submit($form);

        $em->refresh($user);

        $client->followRedirect();

        $encoder = $container->get('security.user_password_encoder.generic');
        $this->assertTrue($encoder->isPasswordValid($user, 'as'));

        $this->assertNull($user->getResetPasswordCode());

        $client->request('GET', '/restorePassword/wrong');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertContains(
            'There is no user with this code or its already expired',
            $client->getResponse()->getContent()
        );

        $crawler = $client->request('GET', '/restorePassword');

        /** @var User $user */
        $user = $this->fixtures->getReference('user2');

        $em->refresh($user);

        $form = $crawler->selectButton('Send')->form();

        $form->get('reset_password')['email']->setValue($user->getEmail());

        $client->submit($form);

        $user->setLastResetAt(new \DateTime("2010-04-10"));

        $em->persist($user);
        $em->flush();

        $em->refresh($user);

        $client->request('GET', '/restorePassword/'.$user->getResetPasswordCode());

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertContains(
            'There is no user with this code or its already expired',
            $client->getResponse()->getContent()
        );
    }

    /**
     *  Testing main logo /logo api
     *
     *  - Login as admin because it will be needed later to change main logo. Then, request to /logo api when main logo
     *is empty.
     *  - Expected to get 200 status code and appropriate response headers about default file plum_tree_logo.jpg.
     *
     *  - Request to /admin/changecontent api, change main logo, and then go back to /logo api when main logo is not
     * empty.
     *  - Expected to get 200 status code and appropriate response headers about uploaded main logo profile.jpg.
     */
    public function testGetMainLogo()
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

        $client->request('HEAD', '/logo');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals(
            'inline; filename="plum_tree_logo.png"',
            $client->getResponse()->headers->all()['content-disposition']['0']
        );
        $this->assertEquals(
            'image/png',
            $client->getResponse()->headers->all()['content-type']['0']
        );

        $crawler = $client->request('GET', '/admin/changecontent');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $path = $client->getContainer()->getParameter('kernel.project_dir').'/var/test_files';

        $form = $crawler->selectButton('Change content')->form();

        /** @var FileFormField $fileInput */
        $fileInput = $form->get('change_content')['mainLogo'];
        $fileInput->upload($path.'/test.png');

        $files = $form->getPhpFiles();
        $files['change_content']['mainLogo']['type'] = 'image/jpeg';
        $files['change_content']['termsOfServices']['type'] = 'image/jpeg';
        $csrf_protection = $form['change_content']['_token'];

        $client->request(
            'POST',
            '/admin/changecontent',
            [
                'change_content' => [
                    '_token' => $csrf_protection->getValue(),
                    'Submit' => true
                ]
            ],
            $files
        );

        $configuration = $em->getRepository(Configuration::class)->findOneBy([]);

        $em->refresh($configuration);

        $this->assertNotNull($configuration->getMainLogo());

        $client->request('HEAD', '/logo');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals(
            'attachment; filename="test.png";',
            $client->getResponse()->headers->all()['content-disposition']['0']
        );
    }
}
