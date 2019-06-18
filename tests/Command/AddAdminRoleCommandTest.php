<?php

namespace App\Tests\Command;

use App\Entity\Associate;
use App\Entity\User;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\ORM\EntityManager;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class AddAdminRoleCommandTest extends WebTestCase
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
            "App\DataFixtures\ORM\LoadUsers"
        ])->getReferenceRepository();
    }

    /**
     *  Testing adding admin role to user command
     *
     *  - Execute command with existing email 'draustinis@gmail.com' with current roles 'ROLE_USER'.
     *  - After command execution expected that user which has email 'draustinis@gmail.com' new set roles are.
     * 'ROLE_ADMIN' and 'ROLE_USER' and expected successful message to appear.
     *  - Execute command with not existing email 'd@gmail.com'.
     *  - After command execution expected error message 'User not found'.
     *  - Execute command with existing email 'justtas14@gmail.com' with current roles 'ROLE_ADMIN' and 'ROLE_USER'.
     *  - After command execution expected error message that user already has admin roles.
     *
     *   @group legacy
     */
    public function testAddAdminRole()
    {
        $em = $this->fixtures->getManager();

        $user = $em->find(User::class, 4);

        $email = $user->getEmail();

        $output = $this->runCommand(
            'app:add:role_admin',
            [
                'email' => $email
            ]
        )->getDisplay();

        $em->refresh($user);

        $this->assertContains('Admin role successfully added', $output);

        $this->assertContains('ROLE_ADMIN', $user->getRoles());

        $email = 'd@gmail.com';

        $output = $this->runCommand(
            'app:add:role_admin',
            [
                'email' => $email
            ]
        )->getDisplay();

        $this->assertContains('User not found', $output);

        $email = 'admin@plumtreesystems.com';

        $user = $em->getRepository(User::class)->findOneBy([
            'email' => $email
        ]);

        $this->assertContains('ROLE_ADMIN', $user->getRoles());

        $output = $this->runCommand(
            'app:add:role_admin',
            [
                'email' => $email
            ]
        )->getDisplay();

        $this->assertContains('User is already admin!', $output);
    }
}
