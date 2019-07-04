<?php


namespace App\Service;

use App\Entity\EmailTemplate;
use App\Exception\UnsupportedEmailTypeException;
use Doctrine\ORM\EntityManagerInterface;
use mysql_xdevapi\Exception;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig_Environment;
use nadar\quill\Lexer;

class EmailTemplateManager
{
    const EMAIL_TYPE_INVITATION = 'INVITATION';
    const EMAIL_TYPE_RESET_PASSWORD = 'RESET_PASSWORD';
    const EMAIL_TYPE_WELCOME = 'WELCOME';

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
                    $emailTemplate->setEmailBody("<br/> Here is your <a href='{{link}}'>link</a> <br/><br/>".
                    "To opt out of this service click <a href='{{ optOutUrl }}'>this</a> link");
                    $emailTemplate->setEmailSubject("You got invited by {{senderName}}. ");
                    $emailTemplate->setEmailType(self::EMAIL_TYPE_INVITATION);
                    break;
                case self::EMAIL_TYPE_RESET_PASSWORD:
                    $emailTemplate->setEmailBody(
                        'To reset your password click <a href="{{ link }}">here</a><br/><br/>'
                    );
                    $emailTemplate->setEmailSubject("Password Reset");
                    $emailTemplate->setEmailType(self::EMAIL_TYPE_RESET_PASSWORD);
                    break;
                case self::EMAIL_TYPE_WELCOME:
                    $emailTemplate->setEmailBody(
                        'Hello {{ name }}, welcome to prelaunch!'
                    );
                    $emailTemplate->setEmailSubject("Welcome");
                    $emailTemplate->setEmailType(self::EMAIL_TYPE_WELCOME);
                    break;
            }
            $this->em->persist($emailTemplate);
            $this->em->flush();
        }
        return $emailTemplate;
    }

    private function getSupportedTypes()
    {
        return [self::EMAIL_TYPE_INVITATION, self::EMAIL_TYPE_RESET_PASSWORD, self::EMAIL_TYPE_WELCOME];
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

        $emailTemplateBodyDelta = $emailTemplateEntity->getEmailBody();

        $parser = new \DBlackborough\Quill\Parser\Html();
        $renderer = new \DBlackborough\Quill\Renderer\Html();

        $parser->load($emailTemplateBodyDelta)->parse();

        $emailTemplateBodyHTML = $renderer->load($parser->deltas())->render();

        $templateSubject = $this->twig->createTemplate($emailTemplateSubject);

        $templateBody = $this->twig->createTemplate($emailTemplateBodyHTML);

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
