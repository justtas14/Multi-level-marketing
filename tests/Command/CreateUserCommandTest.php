<?php

namespace App\Tests\Command;

use App\Entity\Associate;
use App\Entity\User;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\ORM\EntityManager;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class CreateUserCommandTest extends WebTestCase
{
    /**
     * @var ReferenceRepository
     */
    private $fixtures;

    protected function setUp() : void
    {
        parent::setUp();
        $this->fixtures = $this->loadFixtures([
        ])->getReferenceRepository();
    }

    /**
     *  Testing add user command
     *
     *  - Execute command with input email 'email@gmail.com', fullName 'name' and password 'pass'.
     *  - After command execution expected that user has been added with matching roles 'ROLE_USER', 'ROLE_ADMIN',
     * matching password and email. Also expected that associate has been added with matching input fullName and email
     *
     * @covers \App\Command\CreateUserCommand::__construct()
     * @covers \App\Command\CreateUserCommand::configure()
     * @covers \App\Command\CreateUserCommand::execute()
     *
     */
    public function testAddedUser()
    {
        $email = 'email@gmail.com';
        $fullName = 'name';
        $password = 'pass';
        $mobilePhone = '4848648';

        $this->runCommand(
            'app:create:admin',
            [
                'email' => $email,
                'fullName' => $fullName,
                'password' => $password,
                'mobilePhone' => $mobilePhone
            ]
        );

        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        $userRepository = $em->getRepository(User::class);

        $user = $userRepository->findOneBy([
            'email' => $email
            ]);
        $this->assertNotNull($user);

        $container = $this->getContainer();

        $encoder = $container->get('security.user_password_encoder.generic');
        $this->assertTrue($encoder->isPasswordValid($user, 'pass'));
        $this->assertEquals(['ROLE_ADMIN','ROLE_USER'], $user->getRoles());

        /** @var Associate $associate */
        /** @var User $user */
        $associate = $em->getRepository(Associate::class)->find($user->getAssociate()->getId());

        $this->assertNotNull($associate);
        $this->assertEquals('name', $associate->getFullName());
        $this->assertEquals('email@gmail.com', $associate->getEmail());
        $this->assertEquals('4848648', $associate->getMobilePhone());
    }
}
