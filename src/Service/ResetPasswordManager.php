<?php


namespace App\Service;

use App\Entity\Associate;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Swift_Mailer;
use Twig_Environment;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ResetPasswordManager
{
    const SECONDS_UNTIL_EXPIRED = 3600;
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
     * @var Twig_Environment $twig
     */
    private $twig;

    /**
     * @var string
     */
    private $sender;

    public function __construct(
        EntityManagerInterface $entityManager,
        Twig_Environment $twig,
        Swift_Mailer $mailer,
        UrlGeneratorInterface $router,
        string $invitationSender
    ) {
        $this->em = $entityManager;
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->router = $router;
        $this->sender = $invitationSender;
    }

    public function send(string $resetPasswordCode, string $email)
    {
        $link = $this->getResetPasswordUrl($resetPasswordCode);

        $message = new \Swift_Message('Reset Password');

        $message
            ->setSubject("Reset password")
            ->setFrom($this->sender)
            ->setTo($email)
            ->setBody(
                $this->twig->render(
                    'emails/resetPassword.html.twig',
                    ['link' => $link]
                ),
                'text/html'
            );
        $headers = $message->getHeaders();
        $headers->addTextHeader('X-Mailer', 'PHP v'.phpversion());
        $this->mailer->send($message);
    }

    public function getResetPasswordUrl(string $resetPassword) : string
    {
        return $this->router->generate(
            'restore_password',
            ['code' => $resetPassword],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    /**
     * @param string $resetPasswordCode
     * @return User|null
     */
    public function findUser(string $resetPasswordCode): ?User
    {
        if (!$resetPasswordCode) {
            return null;
        }
        $userRepo = $this->em->getRepository(User::class);
        /** @var User $user */
        $user = $userRepo->findOneBy(['resetPasswordCode' => $resetPasswordCode]);

        if (!$user || time() - $user->getLastResetAt()->getTimestamp() > self::SECONDS_UNTIL_EXPIRED) {
            return null;
        }

        return $user;
    }

    public function discardCode(User $user)
    {
        $user->setResetPasswordCode(null);
        $this->em->persist($user);
        $this->em->flush();
    }

    public function resetPassword(User $user)
    {
        $resetCode = md5(time().$user->getId());
        $user->setResetPasswordCode($resetCode);
        $user->setLastResetAt(new \DateTime());
        $this->em->persist($user);
        $this->em->flush();
        $this->send($resetCode, $user->getEmail());
    }
}
