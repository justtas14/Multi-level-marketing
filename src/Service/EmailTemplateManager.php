<?php


namespace App\Service;

use App\Entity\EmailTemplate;
use App\Exception\UnsupportedEmailTypeException;
use Doctrine\ORM\EntityManager;

class EmailTemplateManager
{
    const EMAIL_TYPE_INVITATION = 'INVITATION';

    /**
     * @var EntityManager $em
     */
    private $em;

    public function __construct(
        EntityManager $entityManager
    ) {
        $this->em = $entityManager;
    }

    private function fetchEmailTemplate(string $type)
    {
        $emailTemplateRepository = $this->em->getRepository(EmailTemplate::class);

        $emailTemplate = $emailTemplateRepository->findOneBy(['emailType' => $type]);

        if (!$emailTemplate) {
            $emailTemplate = new EmailTemplate();
            switch ($type) {
                case self::EMAIL_TYPE_INVITATION:
                    $emailTemplate->setEmailBody("<br/> Here is your link {{link}} <br/><br/>");
                    $emailTemplate->setEmailSubject("You got invited by {{senderName}}. ");
                    $emailTemplate->setEmailType(self::EMAIL_TYPE_INVITATION);
                    break;
            }
            $this->em->persist($emailTemplate);
            $this->em->flush();
        }
        return $emailTemplate;
    }

    private function getSupportedTypes()
    {
        return [self::EMAIL_TYPE_INVITATION];
    }

    public function getEmailTemplate(string $type)
    {
        if (in_array($type, $this->getSupportedTypes())) {
            return $this->fetchEmailTemplate($type);
        }
        throw new UnsupportedEmailTypeException('Given email type is not supported');
    }
}
