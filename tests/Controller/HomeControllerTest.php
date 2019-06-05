<?php


namespace App\Tests\Controller;

use App\Entity\Invitation;
use App\Entity\User;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\ORM\EntityManager;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
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
            "App\DataFixtures\ORM\LoadInvitations",

        ])->getReferenceRepository();
    }

    /**
     *  Testing user registration after he gets invitation
     *
     *  - Get invitation2 entity and request to /register/{invitationCode} with appropriate.
     * invitation code of invitation entity. Expected registration form to open.
     *  - Set all correct values in form.
     *  - Expected to get successful registration message and appropriate user and associate to be added in database.
     * with inputed fields.
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
        $this->getContainer();

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
        $form->get('user_registration')['associate']['country']->setValue('LT');
        $form->get('user_registration')['associate']['address']->setValue('blaha');
        $form->get('user_registration')['associate']['city']->setValue('kretinga');
        $form->get('user_registration')['associate']['postcode']->setValue('12345');
        $form->get('user_registration')['associate']['mobilePhone']->setValue('86757');
        $form->get('user_registration')['associate']['homePhone']->setValue('23543');
        $form->get('user_registration')['associate']['agreedToEmailUpdates']->setValue(1);
        $form->get('user_registration')['associate']['agreedToTextMessageUpdates']->setValue(1);
        $form->get('user_registration')['associate']['agreedToSocialMediaUpdates']->setValue(1);
        $form->get('user_registration')['associate']['profilePicture']->setValue(null);

        $client->submit($form);

        $client->followRedirect();

        $session = $client->getContainer()->get('session');
        $messages = $session->getFlashBag()->get('success');

        $this->assertEquals(1, sizeof($messages));

        $userRepository = $em->getRepository(User::class);

        $addedUser = $userRepository->findOneBy(['email' => $invitation->getEmail()]);
        $this->assertEquals($invitation->getFullName(), $addedUser->getAssociate()->getFullName());
        $this->assertEquals('LT', $addedUser->getAssociate()->getCountry());
        $this->assertEquals('blaha', $addedUser->getAssociate()->getAddress());
        $this->assertEquals('kretinga', $addedUser->getAssociate()->getCity());
        $this->assertEquals('12345', $addedUser->getAssociate()->getPostcode());
        $this->assertEquals('86757', $addedUser->getAssociate()->getMobilePhone());
        $this->assertEquals('23543', $addedUser->getAssociate()->getHomePhone());
        $this->assertEquals(true, $addedUser->getAssociate()->isAgreedToEmailUpdates());
        $this->assertEquals(true, $addedUser->getAssociate()->isAgreedToTextMessageUpdates());
        $this->assertEquals(true, $addedUser->getAssociate()->isAgreedToSocialMediaUpdates());
        $this->assertEquals(null, $addedUser->getAssociate()->getProfilePicture());

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

        $form = $crawler->selectButton('Submit')->form();

        $form->get('user_registration')['email']->setValue('justtas14@gmail.com');
        $form->get('user_registration')['plainPassword']['first']->setValue('justtas');
        $form->get('user_registration')['plainPassword']['second']->setValue('justtas');
        $form->get('user_registration')['associate']['fullName']->setValue($invitation->getFullName());
        $form->get('user_registration')['associate']['country']->setValue('LT');
        $form->get('user_registration')['associate']['address']->setValue('blaha');
        $form->get('user_registration')['associate']['city']->setValue('kretinga');
        $form->get('user_registration')['associate']['postcode']->setValue('12345');
        $form->get('user_registration')['associate']['mobilePhone']->setValue('86757');
        $form->get('user_registration')['associate']['homePhone']->setValue('23543');
        $form->get('user_registration')['associate']['agreedToEmailUpdates']->setValue(1);
        $form->get('user_registration')['associate']['agreedToTextMessageUpdates']->setValue(1);
        $form->get('user_registration')['associate']['agreedToSocialMediaUpdates']->setValue(1);
        $form->get('user_registration')['associate']['profilePicture']->setValue(null);

        $crawler = $client->submit($form);

        $this->assertContains(
            'This email already exist',
            $crawler->filter('div.alert-error')->html()
        );

        /** @var Invitation $invitation */
        $invitation = $this->fixtures->getReference('invitation6');

        $em->refresh($invitation);

        $client = $this->makeClient();

        $crawler = $client->request('GET', '/register/'.$invitation->getInvitationCode());

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Submit')->form();

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
        $form->get('user_registration')['associate']['agreedToSocialMediaUpdates']->setValue(1);
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

        $form = $crawler->selectButton('Submit')->form();

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
        $form->get('user_registration')['associate']['agreedToSocialMediaUpdates']->setValue(1);
        $form->get('user_registration')['associate']['profilePicture']->setValue(null);

        $client->submit($form);

        $this->assertContains(
            'This value should not be blank.',
            $client->getResponse()->getContent()
        );
    }
}
