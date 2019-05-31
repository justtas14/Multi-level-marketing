<?php


namespace App\Service;

use App\Entity\EmailTemplate;
use App\Entity\Invitation;
use Doctrine\ORM\EntityManager;
use Twig_Environment;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class InvitationManager
{
    const SECONDS_UNTIL_EXPIRED = 86400;
    /**
     * @var EntityManager $em
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


    public function __construct(
        EntityManager $entityManager,
        Twig_Environment $twig,
        \Swift_Mailer $mailer,
        UrlGeneratorInterface $router
    ) {
        $this->em = $entityManager;
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->router = $router;
    }

    public function send(Invitation $invitation)
    {
        $link = $this->getInvitationUrl($invitation);

        $message = new \Swift_Message('Invitation');

        $emailTemplateEntity = $this->em->getRepository(EmailTemplate::class)
            ->findOneBy(['emailType' => 'INVITATION']);

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
            ->setFrom($invitation->getSender()->getEmail())
            ->setTo($invitation->getEmail())
            ->setBody(
                $templateBody->render(
                    ['link' => $link, 'senderName' => $invitation->getSender()->getFullName()]
                ),
                'text/html'
            );
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

        if ($invitation || $invitation->getUsed() || time() - $invitation->getCreated() > self::SECONDS_UNTIL_EXPIRED) {
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
