<?php


namespace App\Service;

use App\Entity\Associate;
use App\Entity\Invitation;
use Doctrine\ORM\EntityManagerInterface;
use Swift_Mailer;
use Twig_Environment;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class InvitationManager
{
    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    /**
     * @var Swift_Mailer $mailer
     */
    private $mailer;

    /**
     * @var UrlGeneratorInterface $router
     */
    private $router;

    /**
     * @var EmailTemplateManager $emailTemplateManager
     */
    private $emailTemplateManager;

    /**
     * @var int
     */
    private $secondsUntilExpired;

    /**
     * @var string
     */
    private $sender;

    public function __construct(
        EntityManagerInterface $entityManager,
        Swift_Mailer $mailer,
        UrlGeneratorInterface $router,
        EmailTemplateManager $emailTemplateManager,
        string $invitationSender,
        int $secondsUntilExpiredInvitation
    ) {
        $this->em = $entityManager;
        $this->mailer = $mailer;
        $this->router = $router;
        $this->emailTemplateManager = $emailTemplateManager;
        $this->sender = $invitationSender;
        $this->secondsUntilExpired = $secondsUntilExpiredInvitation;
    }

    public function send(Invitation $invitation)
    {
        $link = $this->getInvitationUrl($invitation);

        $params = [
            'link' => $link,
            'receiverName' => $invitation->getFullName(),
            'senderName' => $invitation->getSender()->getFullName(),
            'optOutUrl' => $this->router->generate(
                'opt_out_email',
                ['invitationCode' => $invitation->getInvitationCode()],
                UrlGeneratorInterface::ABSOLUTE_URL
            )
        ];

        $message = $this
            ->emailTemplateManager->createMessage(EmailTemplateManager::EMAIL_TYPE_INVITATION, $params);

        $message
            ->setFrom($this->sender)
            ->setTo($invitation->getEmail());

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

    public function getAssociateUrl($username) : string
    {
        return $this->router->generate(
            'registration',
            ['code' => $username],
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
            || time() - $invitation->getCreated() > $this->secondsUntilExpired) {
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

    /**
     * @param Associate $associate
     */
    public function sendWelcomeEmail(Associate $associate)
    {
        $params = [
            'name' => $associate->getFullName()
        ];

        $message = $this
            ->emailTemplateManager->createMessage(EmailTemplateManager::EMAIL_TYPE_WELCOME, $params);

        $message
            ->setFrom($this->sender)
            ->addCc($associate->getParent()->getEmail())
            ->setTo($associate->getEmail());

        $headers = $message->getHeaders();
        $headers->addTextHeader('X-Mailer', 'PHP v'.phpversion());
        $this->mailer->send($message);
    }
}
