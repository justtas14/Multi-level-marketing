<?php

namespace App\Tests\Command;

use App\Entity\Invitation;
use App\Entity\User;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class RemoveAssociateCommandTest extends WebTestCase
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
            "App\DataFixtures\ORM\LoadInvitations"
        ])->getReferenceRepository();
    }

    /**
     *  Testing remove associate command
     *
     *  - Execute command with admin email which has no children.
     *  - Expected that admin associate has been removed and success message appeared.
     *
     *  - Execute command with admin email which has no children and no associate.
     *  - Expected message to appear that admin doesnt have associate.
     *
     *  - Execute command with not existing email.
     *  - Expected message to appear that user not found.
     *
     *  - Execute command with admin email which has children.
     *  - Expected message to appear that admin has children.
     *
     *  - Execute command with not admin email.
     *  - Expected message to appear that user is not admin.
     *
     * @covers \App\Command\RemoveAssociateCommand::__construct()
     * @covers \App\Command\RemoveAssociateCommand::configure()
     * @covers \App\Command\RemoveAssociateCommand::execute()
     *
     */
    public function testRemoveAssociate()
    {
        $em = $this->fixtures->getManager();

        $user = $em->find(User::class, 22);

        $email = $user->getEmail();

        $output = $this->runCommand(
            'app:remove:associate',
            [
                'email' => $email
            ]
        )->getDisplay();

        $em->refresh($user);

        $this->assertStringContainsString('Associate successfully removed', $output);

        $invitations = $em->getRepository(Invitation::class)->findBy(['sender' => $email]);

        $this->assertEmpty($invitations);

        $this->assertNull($user->getAssociate());

        $output = $this->runCommand(
            'app:remove:associate',
            [
                'email' => $email
            ]
        )->getDisplay();

        $this->assertStringContainsString('Associate does not exist', $output);

        $email = 'd@gmail.com';

        $output = $this->runCommand(
            'app:remove:associate',
            [
                'email' => $email
            ]
        )->getDisplay();

        $this->assertStringContainsString('User not found', $output);

        $user = $em->find(User::class, 1);

        $output = $this->runCommand(
            'app:remove:associate',
            [
                'email' => $user->getEmail()
            ]
        )->getDisplay();

        $this->assertStringContainsString('Associate has children!', $output);

        $user = $em->find(User::class, 2);

        $output = $this->runCommand(
            'app:remove:associate',
            [
                'email' => $user->getEmail()
            ]
        )->getDisplay();

        $this->assertStringContainsString('User is not admin!', $output);
    }
}
