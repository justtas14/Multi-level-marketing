<?php


namespace App\Service;

use App\Entity\Log;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Swift_Mailer;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ResetPasswordManager
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
     * @var string
     */
    private $sender;

    /**
     * @var EmailTemplateManager $emailTemplateManager
     */
    private $emailTemplateManager;

    /**
     * @var int
     */
    private $secondsUntilExpired;

    /**
     * @var Logging $logging
     */
    private $logging;

    public function __construct(
        EntityManagerInterface $entityManager,
        Swift_Mailer $mailer,
        UrlGeneratorInterface $router,
        EmailTemplateManager $emailTemplateManager,
        Logging $logging,
        string $invitationSender,
        string $secondsUntilExpiredResetPassword
    ) {
        $this->em = $entityManager;
        $this->mailer = $mailer;
        $this->router = $router;
        $this->emailTemplateManager = $emailTemplateManager;
        $this->logging = $logging;
        $this->sender = $invitationSender;
        $this->secondsUntilExpired = $secondsUntilExpiredResetPassword;
    }

    public function send(string $resetPasswordCode, string $email)
    {
        $link = $this->getResetPasswordUrl($resetPasswordCode);

        $params = [
            'link' => $link
        ];

        $message = $this
            ->emailTemplateManager->createMessage(EmailTemplateManager::EMAIL_TYPE_RESET_PASSWORD, $params);

        $message
            ->setFrom($this->sender)
            ->setTo($email);
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

        if (!$user || time() - $user->getLastResetAt()->getTimestamp() > $this->secondsUntilExpired) {
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
        $this->logging->createLog(
            'Reset password email was sent to '.$user->getEmail(),
            'Reset password email sending'
        );
    }
}
