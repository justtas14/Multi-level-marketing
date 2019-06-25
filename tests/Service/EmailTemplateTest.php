<?php


namespace App\Tests\Service;

use App\Entity\EmailTemplate;
use App\Exception\UnsupportedEmailTypeException;
use App\Service\EmailTemplateManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

class EmailTemplateTest extends TestCase
{
    /**
     *  Testing EmailTemplateManager service get email method functionality.
     *
     *  - Create new emailTemplate, mock entity manager and repository with new created EmailTemplate and create
     * EmailTemplateManager service with params of mocked entity manager.
     *  - Expected that emailTemplateManager method returns same new first created emailTemplate.
     *
     *  - Mock entity manager and repository with null value create EmailTemplateManager service with params
     * of mocked entity manager.
     *  - Expected that emailTemplateManager method returns default created emailTemplate.
     *
     *  - Create new emailTemplate, but this time with wrong type, mock entity manager and repository with new created
     * EmailTemplate and create EmailTemplateManager service with params of mocked entity manager.
     *  - Expected exception to be thrown because of unsupported email type.
     */
    public function testGetEmailTemplate()
    {
        $emailTemplate = new EmailTemplate();
        $emailTemplate->setEmailSubject("email subject");
        $emailTemplate->setEmailBody("email body");
        $emailTemplate->setEmailType("INVITATION");

        $entityRepository = $this->createMock(EntityRepository::class);
        $entityRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn($emailTemplate);


        $entityManagerMock = $this->createMock(EntityManager::class);

        $entityManagerMock->expects($this->any())
            ->method('getRepository')
            ->willReturn($entityRepository);

        $twigMock = $this->createMock(\Twig_Environment::class);

        $emailTemplateManager = new EmailTemplateManager($entityManagerMock, $twigMock);

        $this->assertEquals($emailTemplate, $emailTemplateManager->getEmailTemplate('INVITATION'));

        $emailTemplate = new EmailTemplate();
        $emailTemplate->setEmailSubject("You got invited by {{senderName}}. ");
        $emailTemplate->setEmailBody("<br/> Here is your link {{link}} <br/><br/>");
        $emailTemplate->setEmailType("INVITATION");

        $entityRepository = $this->createMock(EntityRepository::class);
        $entityRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn(null);

        $entityManagerMock = $this->createMock(EntityManager::class);

        $entityManagerMock->expects($this->any())
            ->method('getRepository')
            ->willReturn($entityRepository);

        $emailTemplateManager = new EmailTemplateManager($entityManagerMock, $twigMock);

        $this->assertEquals($emailTemplate, $emailTemplateManager->getEmailTemplate('INVITATION'));

        $emailTemplate = new EmailTemplate();
        $emailTemplate->setEmailSubject("email subject");
        $emailTemplate->setEmailBody("email body");
        $emailTemplate->setEmailType("UPDATE");


        $entityRepository = $this->createMock(EntityRepository::class);
        $entityRepository->expects($this->any())
            ->method('findOneBy')
            ->willReturn($emailTemplate);


        $entityManagerMock = $this->createMock(EntityManager::class);

        $entityManagerMock->expects($this->any())
            ->method('getRepository')
            ->willReturn($entityRepository);

        $emailTemplateManager = new EmailTemplateManager($entityManagerMock, $twigMock);
        $this->expectException(UnsupportedEmailTypeException::class);
        $emailTemplateManager->getEmailTemplate('UPDATE');
    }
}
