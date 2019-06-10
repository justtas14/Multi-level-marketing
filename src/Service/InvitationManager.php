<?php


namespace App\Service;

use App\Entity\Invitation;
use Doctrine\ORM\EntityManagerInterface;
use Twig_Environment;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class InvitationManager
{
    const SECONDS_UNTIL_EXPIRED = 86400;
    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    /**
     * @var \Swift_Mailer $mailer
     */
    private $mailer;

    /**
     * @var UrlGeneratorInterface $router
     */
    private $router;

    /**
     * @var Twig_Environment $twig
     */
    private $twig;

    /** @var EmailTemplateManager $emailTemplateManager */
    private $emailTemplateManager;

    /**
     * @var string
     */
    private $sender;

    public function __construct(
        EntityManagerInterface $entityManager,
        Twig_Environment $twig,
        \Swift_Mailer $mailer,
        UrlGeneratorInterface $router,
        EmailTemplateManager $emailTemplateManager,
        string $invitationSender
    ) {
        $this->em = $entityManager;
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->router = $router;
        $this->emailTemplateManager = $emailTemplateManager;
        $this->sender = $invitationSender;
    }

    public function send(Invitation $invitation)
    {
        $link = $this->getInvitationUrl($invitation);

        $message = new \Swift_Message('Invitation');

        $emailTemplateEntity = $this
            ->emailTemplateManager->getEmailTemplate(EmailTemplateManager::EMAIL_TYPE_INVITATION);

        $emailTemplateSubject = $emailTemplateEntity->getEmailSubject();

        $emailTemplateBody = $emailTemplateEntity->getEmailBody();

        $templateSubject = $this->twig->createTemplate($emailTemplateSubject);

        $templateBody = $this->twig->createTemplate($emailTemplateBody);

        $message
            ->setSubject(
                $templateSubject->render(
                    ['link' => $link, 'senderName' => $invitation->getSender()->getFullName()]
                )
            )
            ->setFrom($this->sender)
            ->setTo($invitation->getEmail())
            ->setBody(
                $templateBody->render(
                    ['link' => $link, 'senderName' => $invitation->getSender()->getFullName()]
                ),
                'text/html'
            );
        $headers = $message->getHeaders();
        $headers->addTextHeader('X-Mailer', 'PHP v'.phpversion());
        $this->mailer->send($message);
    }

    public function getInvitationUrl(Invitation $invitation) : string
    {
        return $this->router->generate(
            'registration',
            ['code' => $invitation->getInvitationCode()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    /**
     * @param string $invitationCode
     * @return Invitation|null
     */
    public function findInvitation(string $invitationCode): ?Invitation
    {
        $invitationRepo = $this->em->getRepository(Invitation::class);
        $invitation = $invitationRepo->findOneBy(['invitationCode' => $invitationCode]);

        if (!$invitation || $invitation->getUsed()
            || time() - $invitation->getCreated() > self::SECONDS_UNTIL_EXPIRED) {
            return null;
        }

        return $invitation;
    }

    /**
     * @param Invitation $invitation
     */
    public function discardInvitation(Invitation $invitation): void
    {
        $invitation->setUsed(true);
    }
}
