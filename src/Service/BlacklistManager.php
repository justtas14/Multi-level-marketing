<?php


namespace App\Service;

use App\Entity\InvitationBlacklist;
use App\Exception\NotFoundInvitationException;
use App\Repository\InvitationBlacklistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class BlacklistManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var InvitationBlacklistRepository
     */
    private $repo;

    /**
     * @var InvitationManager
     */
    private $invitationManager;

    /**
     * @var LoggerInterface $databaseLogger
     */
    private $databaseLogger;

    /**
     * BlacklistManager constructor.
     * @param EntityManagerInterface $em
     * @param InvitationManager $invitationManager
     * @param LoggerInterface $databaseLogger
     */
    public function __construct(
        EntityManagerInterface $em,
        InvitationManager $invitationManager,
        LoggerInterface $databaseLogger
    ) {
        $this->em = $em;
        $this->repo = $em->getRepository(InvitationBlacklist::class);
        $this->invitationManager = $invitationManager;
        $this->databaseLogger = $databaseLogger;
    }

    public function existsInBlacklist(string $email): bool
    {
        $results = $this->repo->findBy(['email' => $email]);
        return sizeof($results);
    }

    public function existsInBlacklistByCode(string $invitationCode): bool
    {
        $invitation = $this->invitationManager->findInvitation($invitationCode);
        if (!$invitation) {
            throw new NotFoundInvitationException();
        }
        return $this->existsInBlacklist($invitation->getEmail());
    }

    public function addToBlacklist($invitationCode)
    {
        $invitation = $this->invitationManager->findInvitation($invitationCode);
        if (!$this->existsInBlacklist($invitation->getEmail())) {
            $this->em->persist((new InvitationBlacklist())->setEmail($invitation->getEmail()));
            $this->em->flush();

            $this->databaseLogger->info($invitation->getEmail().' email added to blacklist ');
        }
        return;
    }
}
