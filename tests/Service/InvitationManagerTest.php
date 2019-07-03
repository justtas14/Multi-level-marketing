<?php


namespace App\Tests\Service;

use App\Entity\Invitation;
use App\Service\EmailTemplateManager;
use App\Service\InvitationManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use Swift_Mailer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class InvitationManagerTest extends TestCase
{
    /**
     *  Testing service InvitationManager findInvitation method functionality.
     *
     *  - Create new with correct data invitation entity, mock needed services and methods, mock findOneBy method that
     * it will return new created invitation, and call invitationManager findInvitation method.
     *  - After call expected to get the same created invitation.
     *
     *  - Create new with correct data invitation entity except setCreated method value, which is expired as it set
     * more than one week ago, mock needed services and methods, mock findOneBy method that it will return
     * new created invitation entity, and call invitationManager findInvitation method.
     *  - After call expected to get null because setCreated is set more than one week ago which is clearly expired.
     *
     *  - Create new with correct data invitation entity and this time with not expired setCreated method value,
     * which is set less then one week ago, mock needed services and methods, mock findOneBy method that it will
     * return new created invitation entity, and call invitationManager findInvitation method.
     *  - After call expected to get the same created invitation.
     *
     *  - Create new with correct data invitation entity  mock needed services and methods, mock findOneBy
     * method that it will return null, and call invitationManager findInvitation method.
     *  - After call expected to get null.
     *
     *  - Create new with correct data invitation entity except setUsed method value, which is set to true,
     * mock needed services and methods, mock findOneBy method that it will return new created invitation entity,
     * and call invitationManager findInvitation method.
     *  - After call expected to get null because setUsed method is true.
     */
    public function testInvitationFindUser()
    {
        $invitation = new Invitation();

        $invitation->setId(1);
        $invitation->setEmail('james@gmail.com');
        $invitation->setCreated(time());
        $invitation->setInvitationCode("code");
        $invitation->setFullName("James");

        $em = $this->createMock(EntityManager::class);
        $mailer = $this->createMock(Swift_Mailer::class);
        $router = $this->createMock(UrlGeneratorInterface::class);
        $emailTemplateManager = $this->createMock(EmailTemplateManager::class);

        $entityRepository = $this->createMock(EntityRepository::class);
        $entityRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn($invitation);

        $em->expects($this->any())
            ->method('getRepository')
            ->willReturn($entityRepository);

        $invitationManager = new InvitationManager(
            $em,
            $mailer,
            $router,
            $emailTemplateManager,
            'noreply@plumtreesystems.com',
            604800
        );

        $this->assertEquals($invitation, $invitationManager->findInvitation($invitation->getInvitationCode()));

        $invitation = new Invitation();

        $invitation->setId(1);
        $invitation->setEmail('james@gmail.com');
        $invitation->setCreated(time() - 604810);
        $invitation->setInvitationCode("code");
        $invitation->setFullName("James");

        $em = $this->createMock(EntityManager::class);

        $entityRepository = $this->createMock(EntityRepository::class);
        $entityRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn($invitation);

        $em->expects($this->any())
            ->method('getRepository')
            ->willReturn($entityRepository);

        $invitationManager = new InvitationManager(
            $em,
            $mailer,
            $router,
            $emailTemplateManager,
            'noreply@plumtreesystems.com',
            604800
        );
        $this->assertNull($invitationManager->findInvitation($invitation->getInvitationCode()));

        $invitation = new Invitation();

        $invitation->setId(1);
        $invitation->setEmail('james@gmail.com');
        $invitation->setCreated(time() - 604600);
        $invitation->setInvitationCode("code");
        $invitation->setFullName("James");

        $em = $this->createMock(EntityManager::class);

        $entityRepository = $this->createMock(EntityRepository::class);
        $entityRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn($invitation);

        $em->expects($this->any())
            ->method('getRepository')
            ->willReturn($entityRepository);

        $invitationManager = new InvitationManager(
            $em,
            $mailer,
            $router,
            $emailTemplateManager,
            'noreply@plumtreesystems.com',
            604800
        );
        $this->assertEquals($invitation, $invitationManager->findInvitation($invitation->getInvitationCode()));

        $invitation = new Invitation();

        $invitation->setId(1);
        $invitation->setEmail('james@gmail.com');
        $invitation->setCreated(time() - 604600);
        $invitation->setInvitationCode("code");
        $invitation->setFullName("James");

        $em = $this->createMock(EntityManager::class);

        $entityRepository = $this->createMock(EntityRepository::class);
        $entityRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn(null);

        $em->expects($this->any())
            ->method('getRepository')
            ->willReturn($entityRepository);

        $invitationManager = new InvitationManager(
            $em,
            $mailer,
            $router,
            $emailTemplateManager,
            'noreply@plumtreesystems.com',
            604800
        );
        $this->assertNull($invitationManager->findInvitation($invitation->getInvitationCode()));

        $invitation = new Invitation();

        $invitation->setId(1);
        $invitation->setEmail('james@gmail.com');
        $invitation->setCreated(time());
        $invitation->setInvitationCode("code");
        $invitation->setFullName("James");
        $invitation->setUsed(true);

        $em = $this->createMock(EntityManager::class);

        $entityRepository = $this->createMock(EntityRepository::class);
        $entityRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn($invitation);

        $em->expects($this->any())
            ->method('getRepository')
            ->willReturn($entityRepository);

        $invitationManager = new InvitationManager(
            $em,
            $mailer,
            $router,
            $emailTemplateManager,
            'noreply@plumtreesystems.com',
            604800
        );
        $this->assertNull($invitationManager->findInvitation($invitation->getInvitationCode()));
    }
}
