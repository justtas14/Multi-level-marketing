<?php

namespace App\Tests\Command;

use App\Entity\Associate;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use App\Command\CreateUserCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CreateUserCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('app:create:admin');
        $commandTester = new CommandTester($command);

        $email = 'email@gmail.com';
        $fullName = 'name';
        $password = 'pass';

        $commandTester->execute([
           'command' => $command->getName(),
           'email' => $email,
            'fullName' => $fullName,
            'password' => $password
        ]);

        /** @var EntityManager $em */
        $em = $kernel->getContainer()->get('doctrine.orm.default_entity_manager');

        $userRepository = $em->getRepository(User::class);
        $roles = ['ROLE_ADMIN', 'ROLE_USER'];
        $user = $userRepository->findOneBy(
            ['email' => $email, 'roles' => $roles]
        );
        $this->assertNotNull($user);

        /** @var Associate $associate */
        $associate = $user->getAssociate();

        $this->assertEquals('name', $associate->getFullName());
        $this->assertEquals('email@gmail.com', $associate->getEmail());
    }
}
