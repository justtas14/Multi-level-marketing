<?php


namespace App\Service;

use App\Entity\Invitation;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig_Environment;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class InvitationManager
{
    const secondsUntilExpired = 86400;
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
        $link = $this->router->generate(
            'registration',
            ['code' => $invitation->getInvitationCode()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $message = new \Swift_Message('Invitation');
        $message
            ->setFrom($invitation->getSender()->getEmail())
            ->setTo($invitation->getEmail())
            ->setBody(
                $this->twig->render(
                    'emails/registration.html.twig',
                    ['link' => $link]
                ),
                'text/html'
            );

        $this->mailer->send($message);
    }

    /**
     * @param string $invitationCode
     * @return Invitation|null
     */
    public function findInvitation(string $invitationCode): ?Invitation
    {
        $invitationRepo = $this->em->getRepository(Invitation::class);
        $invitation = $invitationRepo->findOneBy(['invitationCode' => $invitationCode]);

        if (!$invitation) {
            throw new NotFoundHttpException('Invitation not found');
        }

        if ($invitation->getUsed() || time() - $invitation->getCreated() > self::secondsUntilExpired) {
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