<?php


namespace App\Service;

use App\Entity\EmailTemplate;
use App\Exception\UnsupportedEmailTypeException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig_Environment;

class EmailTemplateManager
{
    const EMAIL_TYPE_INVITATION = 'INVITATION';
    const EMAIL_TYPE_RESET_PASSWORD = 'RESET_PASSWORD';

    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    /**
     * @var Twig_Environment $twig
     */
    private $twig;

    public function __construct(
        EntityManagerInterface $entityManager,
        Twig_Environment $twig
    ) {
        $this->em = $entityManager;
        $this->twig = $twig;
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
                case self::EMAIL_TYPE_RESET_PASSWORD:
                    $emailTemplate->setEmailBody(
                        'To reset your password click here <a href="{{ link }}">{{ link }}</a><br/><br/>'
                    );
                    $emailTemplate->setEmailSubject("Password Reset");
                    $emailTemplate->setEmailType(self::EMAIL_TYPE_RESET_PASSWORD);
                    break;
            }
            $this->em->persist($emailTemplate);
            $this->em->flush();
        }
        return $emailTemplate;
    }

    private function getSupportedTypes()
    {
        return [self::EMAIL_TYPE_INVITATION, self::EMAIL_TYPE_RESET_PASSWORD];
    }

    public function getEmailTemplate(string $type)
    {
        if (in_array($type, $this->getSupportedTypes())) {
            return $this->fetchEmailTemplate($type);
        }
        throw new UnsupportedEmailTypeException('Given email type is not supported');
    }

    public function createMessage(string $type, $params = []) : \Swift_Message
    {
        $emailTemplateEntity = $this->getEmailTemplate($type);

        $emailTemplateSubject = $emailTemplateEntity->getEmailSubject();

        $emailTemplateBody = $emailTemplateEntity->getEmailBody();

        $templateSubject = $this->twig->createTemplate($emailTemplateSubject);

        $templateBody = $this->twig->createTemplate($emailTemplateBody);

        $message = new \Swift_Message();

        $message
            ->setSubject(
                $templateSubject->render($params)
            )
            ->setBody(
                $templateBody->render($params),
                'text/html'
            );

        return $message;
    }
}
