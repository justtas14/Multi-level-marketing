<?php


namespace App\Tests\Service;

use App\Entity\User;
use App\Service\EmailTemplateManager;
use App\Service\ResetPasswordManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Swift_Mailer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ResetPasswordManagerTest extends TestCase
{
    /**
     *  Testing service ResetPasswordManager findUser method functionality.
     *
     *  - Create new with correct data user, mock needed services and methods, mock findOneBy method that it will return
     * new created user, and call resetPasswordManager find user method.
     *  - After call expected to get the same created user.
     *
     *  - Create new with correct data user except lastResetCode, which is expired, mock needed services and methods,
     * mock findOneBy method that it will return new created user, and call resetPasswordManager find user method.
     *  - After call expected to get null because lastResetCode is set "2010-05-04" which is clearly expired.
     *
     *  - Create new with correct data user except lastResetCode, which is expired as it set more than one hour before,
     * mock needed services and methods, mock findOneBy method that it will return new created user, and call
     * resetPasswordManager find user method.
     *  - After call expected to get null because lastResetCode is set more than one hour ago which is expired.
     *
     *  - Create new with correct data user, but this time lastResetCode is set less than one hour ago, which is not
     * expired. Mock needed services and methods, mock findOneBy method that it will return new created user, and call
     * resetPasswordManager find user method.
     *  - After call expected to get the same user because lastResetCode is still not expired.
     *
     *  - Create new with correct data user, mock needed services and methods, mock findOneBy method that it will return
     * new created user, and call resetPasswordManager find user method but this time with empty resetPasswordCode
     * in params.
     *  - After call expected to get null because resetPasswordCode is empty.
     */
    public function testResetPasswordFindUser()
    {
        $user = new User();

        $user->setId(1);
        $user->setResetPasswordCode("code");
        $user->setLastResetAt(new \DateTime());
        $user->setEmail('user@gmail.com');
        $user->setPlainPassword("user");

        $em = $this->createMock(EntityManager::class);
        $mailer = $this->createMock(Swift_Mailer::class);
        $router = $this->createMock(UrlGeneratorInterface::class);
        $emailTemplateManager = $this->createMock(EmailTemplateManager::class);
        $databaseLogger = $this->createMock(LoggerInterface::class);

        $entityRepository = $this->createMock(EntityRepository::class);
        $entityRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn($user);

        $em->expects($this->any())
            ->method('getRepository')
            ->willReturn($entityRepository);

        $resetPasswordManager = new ResetPasswordManager(
            $em,
            $mailer,
            $router,
            $emailTemplateManager,
            'noreply@plumtreesystems.com',
            3600,
            $databaseLogger
        );

        $this->assertEquals($user, $resetPasswordManager->findUser($user->getResetPasswordCode()));

        $user = new User();

        $user->setId(1);
        $user->setResetPasswordCode("code");
        $user->setLastResetAt(new \DateTime("2010-05-04"));
        $user->setEmail('user@gmail.com');
        $user->setPlainPassword("user");

        $em = $this->createMock(EntityManager::class);

        $entityRepository = $this->createMock(EntityRepository::class);
        $entityRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn($user);

        $em->expects($this->any())
            ->method('getRepository')
            ->willReturn($entityRepository);

        $resetPasswordManager = new ResetPasswordManager(
            $em,
            $mailer,
            $router,
            $emailTemplateManager,
            'noreply@plumtreesystems.com',
            3600,
            $databaseLogger
        );
        $this->assertNull($resetPasswordManager->findUser($user->getResetPasswordCode()));

        $user = new User();

        $timestamp = time() - 3700;
        $date = new \DateTime();
        $date->setTimestamp($timestamp);

        $user->setId(1);
        $user->setResetPasswordCode("code");
        $user->setLastResetAt($date);
        $user->setEmail('user@gmail.com');
        $user->setPlainPassword("user");

        $em = $this->createMock(EntityManager::class);

        $entityRepository = $this->createMock(EntityRepository::class);
        $entityRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn($user);

        $em->expects($this->any())
            ->method('getRepository')
            ->willReturn($entityRepository);

        $resetPasswordManager = new ResetPasswordManager(
            $em,
            $mailer,
            $router,
            $emailTemplateManager,
            'noreply@plumtreesystems.com',
            3600,
            $databaseLogger
        );
        $this->assertNull($resetPasswordManager->findUser($user->getResetPasswordCode()));

        $user = new User();

        $timestamp = time() - 3550;
        $date = new \DateTime();
        $date->setTimestamp($timestamp);

        $user->setId(1);
        $user->setResetPasswordCode("code");
        $user->setLastResetAt($date);
        $user->setEmail('user@gmail.com');
        $user->setPlainPassword("user");

        $em = $this->createMock(EntityManager::class);

        $entityRepository = $this->createMock(EntityRepository::class);
        $entityRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn($user);

        $em->expects($this->any())
            ->method('getRepository')
            ->willReturn($entityRepository);

        $resetPasswordManager = new ResetPasswordManager(
            $em,
            $mailer,
            $router,
            $emailTemplateManager,
            'noreply@plumtreesystems.com',
            3600,
            $databaseLogger
        );
        $this->assertEquals($user, $resetPasswordManager->findUser($user->getResetPasswordCode()));

        $user = new User();

        $user->setId(1);
        $user->setResetPasswordCode("code");
        $user->setLastResetAt(new \DateTime());
        $user->setEmail('user@gmail.com');
        $user->setPlainPassword("user");

        $em = $this->createMock(EntityManager::class);

        $entityRepository = $this->createMock(EntityRepository::class);
        $entityRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn($user);

        $em->expects($this->any())
            ->method('getRepository')
            ->willReturn($entityRepository);

        $resetPasswordManager = new ResetPasswordManager(
            $em,
            $mailer,
            $router,
            $emailTemplateManager,
            'noreply@plumtreesystems.com',
            3600,
            $databaseLogger
        );
        $this->assertNull($resetPasswordManager->findUser(""));
    }
}
