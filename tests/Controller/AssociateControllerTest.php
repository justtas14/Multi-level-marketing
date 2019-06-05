<?php


namespace App\Tests\Controller;

use App\Entity\Invitation;
use App\Entity\User;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\ORM\EntityManager;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class AssociateControllerTest extends WebTestCase
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
     *  Testing user logged in update associate form if it updates correctly
     *
     *  - Input all assumed correct update values in form.
     *  - Expected for user to be updated all appropriate inputed values without any error.
     *  - Input all correct values except email which is already taken.
     *  - Expected flash message error to appear which states that email is already taken.
     *  - Input all correct values except old password which is not correct.
     *  - Expected flash message error to appear which states that old password is not correct.
     *  - Input all correct values except repeated new password is left blank.
     *  - After update expected that user password is not updated to blank but left with old password.
     */
    public function testUpdateAssociate()
    {
        $container = $this->getContainer();

        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var User $user */
        $user = $this->fixtures->getReference('user2');

        $em->refresh($user);
        $this->loginAs($user, 'main');

        $client = $this->makeClient();

        $crawler = $client->request('GET', '/associate/profile');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Save')->form();

        $form->get('user_update')['email']->setValue($user->getEmail());
        $form->get('user_update')['oldPassword']->setValue('vanagas');
        $form->get('user_update')['newPassword']['first']->setValue('justtas');
        $form->get('user_update')['newPassword']['second']->setValue('justtas');
        $form->get('user_update')['associate']['fullName']->setValue('Justas');
        $form->get('user_update')['associate']['country']->setValue('LT');
        $form->get('user_update')['associate']['address']->setValue('blaha');
        $form->get('user_update')['associate']['city']->setValue('kretinga');
        $form->get('user_update')['associate']['postcode']->setValue('12345');
        $form->get('user_update')['associate']['mobilePhone']->setValue('86757');
        $form->get('user_update')['associate']['homePhone']->setValue('23543');
        $form->get('user_update')['associate']['agreedToEmailUpdates']->setValue(1);
        $form->get('user_update')['associate']['agreedToTextMessageUpdates']->setValue(1);
        $form->get('user_update')['associate']['agreedToSocialMediaUpdates']->setValue(1);
        $form->get('user_update')['associate']['profilePicture']->setValue(null);

        $client->submit($form);

        $em->refresh($user);

        $this->assertEquals('vanagas@gmail.com', $user->getEmail());

        $encoder = $container->get('security.user_password_encoder.generic');
        $this->assertTrue($encoder->isPasswordValid($user, 'justtas'));

        $this->assertEquals('Justas', $user->getAssociate()->getFullName());
        $this->assertEquals('LT', $user->getAssociate()->getCountry());
        $this->assertEquals('blaha', $user->getAssociate()->getAddress());
        $this->assertEquals('kretinga', $user->getAssociate()->getCity());
        $this->assertEquals('12345', $user->getAssociate()->getPostcode());
        $this->assertEquals('86757', $user->getAssociate()->getMobilePhone());
        $this->assertEquals('23543', $user->getAssociate()->getHomePhone());
        $this->assertEquals(true, $user->getAssociate()->isAgreedToEmailUpdates());
        $this->assertEquals(true, $user->getAssociate()->isAgreedToTextMessageUpdates());
        $this->assertEquals(true, $user->getAssociate()->isAgreedToSocialMediaUpdates());
        $this->assertEquals(null, $user->getAssociate()->getProfilePicture());


        $crawler = $client->request('GET', '/associate/profile');

        $form = $crawler->selectButton('Submit')->form();

        $form->get('user_update')['email']->setValue("justtas14@gmail.com");
        $form->get('user_update')['oldPassword']->setValue('justtas14');
        $form->get('user_update')['newPassword']['first']->setValue('justtas');
        $form->get('user_update')['newPassword']['second']->setValue('justtas');
        $form->get('user_update')['associate']['fullName']->setValue('Justas');
        $form->get('user_update')['associate']['country']->setValue('LT');
        $form->get('user_update')['associate']['address']->setValue('blaha');
        $form->get('user_update')['associate']['city']->setValue('kretinga');
        $form->get('user_update')['associate']['postcode']->setValue('12345');
        $form->get('user_update')['associate']['mobilePhone']->setValue('86757');
        $form->get('user_update')['associate']['homePhone']->setValue('23543');
        $form->get('user_update')['associate']['agreedToEmailUpdates']->setValue(1);
        $form->get('user_update')['associate']['agreedToTextMessageUpdates']->setValue(1);
        $form->get('user_update')['associate']['agreedToSocialMediaUpdates']->setValue(1);
        $form->get('user_update')['associate']['profilePicture']->setValue(null);

        $crawler = $client->submit($form);

        $em->refresh($user);

        $this->assertContains(
            'This email already exist',
            $crawler->filter('div.alert-error')->html()
        );

        $crawler = $client->request('GET', '/associate/profile');

        $form = $crawler->selectButton('Submit')->form();

        $form->get('user_update')['email']->setValue($user->getEmail());
        $form->get('user_update')['oldPassword']->setValue('somepasw');
        $form->get('user_update')['newPassword']['first']->setValue('justtas');
        $form->get('user_update')['newPassword']['second']->setValue('justtas');
        $form->get('user_update')['associate']['fullName']->setValue('Justas');
        $form->get('user_update')['associate']['country']->setValue('LT');
        $form->get('user_update')['associate']['address']->setValue('blaha');
        $form->get('user_update')['associate']['city']->setValue('kretinga');
        $form->get('user_update')['associate']['postcode']->setValue('12345');
        $form->get('user_update')['associate']['mobilePhone']->setValue('86757');
        $form->get('user_update')['associate']['homePhone']->setValue('23543');
        $form->get('user_update')['associate']['agreedToEmailUpdates']->setValue(1);
        $form->get('user_update')['associate']['agreedToTextMessageUpdates']->setValue(1);
        $form->get('user_update')['associate']['agreedToSocialMediaUpdates']->setValue(1);
        $form->get('user_update')['associate']['profilePicture']->setValue(null);

        $crawler = $client->submit($form);

        $em->refresh($user);

        $this->assertContains(
            'Old password is not correct',
            $crawler->filter('div.alert-error')->html()
        );

        $crawler = $client->request('GET', '/associate/profile');

        $form = $crawler->selectButton('Submit')->form();

        $form->get('user_update')['email']->setValue($user->getEmail());
        $form->get('user_update')['oldPassword']->setValue('justtas');
        $form->get('user_update')['newPassword']['first']->setValue('');
        $form->get('user_update')['newPassword']['second']->setValue('');
        $form->get('user_update')['associate']['fullName']->setValue('Justas');
        $form->get('user_update')['associate']['country']->setValue('LT');
        $form->get('user_update')['associate']['address']->setValue('blaha');
        $form->get('user_update')['associate']['city']->setValue('kretinga');
        $form->get('user_update')['associate']['postcode']->setValue('12345');
        $form->get('user_update')['associate']['mobilePhone']->setValue('86757');
        $form->get('user_update')['associate']['homePhone']->setValue('23543');
        $form->get('user_update')['associate']['agreedToEmailUpdates']->setValue(1);
        $form->get('user_update')['associate']['agreedToTextMessageUpdates']->setValue(1);
        $form->get('user_update')['associate']['agreedToSocialMediaUpdates']->setValue(1);
        $form->get('user_update')['associate']['profilePicture']->setValue(null);

        $client->submit($form);

        $em->refresh($user);

        $encoder = $container->get('security.user_password_encoder.generic');
        $this->assertTrue($encoder->isPasswordValid($user, 'justtas'));
    }

    /**
     *  Testing admin logged in update associate form if it updates correctly
     *
     *  - Input all assumed correct update values in form.
     *  - Expected for user to be updated all appropriate inputed values without any error.
     *  - Input all correct values except email which is already taken.
     *  - Expected flash message error to appear which states that email is already taken.
     *  - Input all correct values except old password which is not correct.
     *  - Expected flash message error to appear which states that old password is not correct.
     *  - Input all correct values except repeated new password is left blank.
     *  - After update expected that user password is not updated to blank but left with old password.
     */
    public function testAdminUpdateAssociate()
    {
        $container = $this->getContainer();

        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var User $user */
        $user = $this->fixtures->getReference('user1');

        $em->refresh($user);
        $this->loginAs($user, 'main');

        $client = $this->makeClient();

        $crawler = $client->request('GET', '/associate/profile');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('Save')->form();

        $form->get('user_update')['email']->setValue($user->getEmail());
        $form->get('user_update')['oldPassword']->setValue('justtas14');
        $form->get('user_update')['newPassword']['first']->setValue('justtas');
        $form->get('user_update')['newPassword']['second']->setValue('justtas');
        $form->get('user_update')['associate']['fullName']->setValue('Justas');
        $form->get('user_update')['associate']['country']->setValue('LT');
        $form->get('user_update')['associate']['address']->setValue('blaha');
        $form->get('user_update')['associate']['city']->setValue('kretinga');
        $form->get('user_update')['associate']['postcode']->setValue('12345');
        $form->get('user_update')['associate']['mobilePhone']->setValue('86757');
        $form->get('user_update')['associate']['homePhone']->setValue('23543');
        $form->get('user_update')['associate']['agreedToEmailUpdates']->setValue(1);
        $form->get('user_update')['associate']['agreedToTextMessageUpdates']->setValue(1);
        $form->get('user_update')['associate']['agreedToSocialMediaUpdates']->setValue(1);
        $form->get('user_update')['associate']['profilePicture']->setValue(null);

        $client->submit($form);

        $em->refresh($user);

        $this->assertEquals('justtas14@gmail.com', $user->getEmail());

        $encoder = $container->get('security.user_password_encoder.generic');
        $this->assertTrue($encoder->isPasswordValid($user, 'justtas'));

        $this->assertEquals('Justas', $user->getAssociate()->getFullName());
        $this->assertEquals('LT', $user->getAssociate()->getCountry());
        $this->assertEquals('blaha', $user->getAssociate()->getAddress());
        $this->assertEquals('kretinga', $user->getAssociate()->getCity());
        $this->assertEquals('12345', $user->getAssociate()->getPostcode());
        $this->assertEquals('86757', $user->getAssociate()->getMobilePhone());
        $this->assertEquals('23543', $user->getAssociate()->getHomePhone());
        $this->assertEquals(true, $user->getAssociate()->isAgreedToEmailUpdates());
        $this->assertEquals(true, $user->getAssociate()->isAgreedToTextMessageUpdates());
        $this->assertEquals(true, $user->getAssociate()->isAgreedToSocialMediaUpdates());
        $this->assertEquals(null, $user->getAssociate()->getProfilePicture());


        $crawler = $client->request('GET', '/associate/profile');

        $form = $crawler->selectButton('Submit')->form();

        $form->get('user_update')['email']->setValue("vanagas@gmail.com");
        $form->get('user_update')['oldPassword']->setValue('justtas14');
        $form->get('user_update')['newPassword']['first']->setValue('justtas');
        $form->get('user_update')['newPassword']['second']->setValue('justtas');
        $form->get('user_update')['associate']['fullName']->setValue('Justas');
        $form->get('user_update')['associate']['country']->setValue('LT');
        $form->get('user_update')['associate']['address']->setValue('blaha');
        $form->get('user_update')['associate']['city']->setValue('kretinga');
        $form->get('user_update')['associate']['postcode']->setValue('12345');
        $form->get('user_update')['associate']['mobilePhone']->setValue('86757');
        $form->get('user_update')['associate']['homePhone']->setValue('23543');
        $form->get('user_update')['associate']['agreedToEmailUpdates']->setValue(1);
        $form->get('user_update')['associate']['agreedToTextMessageUpdates']->setValue(1);
        $form->get('user_update')['associate']['agreedToSocialMediaUpdates']->setValue(1);
        $form->get('user_update')['associate']['profilePicture']->setValue(null);

        $crawler = $client->submit($form);

        $em->refresh($user);

        $this->assertContains(
            'This email already exist',
            $crawler->filter('div.alert-error')->html()
        );

        $crawler = $client->request('GET', '/associate/profile');

        $form = $crawler->selectButton('Submit')->form();

        $form->get('user_update')['email']->setValue($user->getEmail());
        $form->get('user_update')['oldPassword']->setValue('somepasw');
        $form->get('user_update')['newPassword']['first']->setValue('justtas');
        $form->get('user_update')['newPassword']['second']->setValue('justtas');
        $form->get('user_update')['associate']['fullName']->setValue('Justas');
        $form->get('user_update')['associate']['country']->setValue('LT');
        $form->get('user_update')['associate']['address']->setValue('blaha');
        $form->get('user_update')['associate']['city']->setValue('kretinga');
        $form->get('user_update')['associate']['postcode']->setValue('12345');
        $form->get('user_update')['associate']['mobilePhone']->setValue('86757');
        $form->get('user_update')['associate']['homePhone']->setValue('23543');
        $form->get('user_update')['associate']['agreedToEmailUpdates']->setValue(1);
        $form->get('user_update')['associate']['agreedToTextMessageUpdates']->setValue(1);
        $form->get('user_update')['associate']['agreedToSocialMediaUpdates']->setValue(1);
        $form->get('user_update')['associate']['profilePicture']->setValue(null);

        $crawler = $client->submit($form);

        $em->refresh($user);

        $this->assertContains(
            'Old password is not correct',
            $crawler->filter('div.alert-error')->html()
        );

        $crawler = $client->request('GET', '/associate/profile');

        $form = $crawler->selectButton('Submit')->form();

        $form->get('user_update')['email']->setValue($user->getEmail());
        $form->get('user_update')['oldPassword']->setValue('justtas');
        $form->get('user_update')['newPassword']['first']->setValue('');
        $form->get('user_update')['newPassword']['second']->setValue('');
        $form->get('user_update')['associate']['fullName']->setValue('Justas');
        $form->get('user_update')['associate']['country']->setValue('LT');
        $form->get('user_update')['associate']['address']->setValue('blaha');
        $form->get('user_update')['associate']['city']->setValue('kretinga');
        $form->get('user_update')['associate']['postcode']->setValue('12345');
        $form->get('user_update')['associate']['mobilePhone']->setValue('86757');
        $form->get('user_update')['associate']['homePhone']->setValue('23543');
        $form->get('user_update')['associate']['agreedToEmailUpdates']->setValue(1);
        $form->get('user_update')['associate']['agreedToTextMessageUpdates']->setValue(1);
        $form->get('user_update')['associate']['agreedToSocialMediaUpdates']->setValue(1);
        $form->get('user_update')['associate']['profilePicture']->setValue(null);

        $client->submit($form);

        $em->refresh($user);

        $encoder = $container->get('security.user_password_encoder.generic');
        $this->assertTrue($encoder->isPasswordValid($user, 'justtas'));
    }
}
