<?php


namespace App\Service;

use App\Entity\InvitationBlacklist;
use App\Exception\NotFoundInvitationException;
use App\Repository\InvitationBlacklistRepository;
use Doctrine\ORM\EntityManagerInterface;

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
     * BlacklistManager constructor.
     * @param EntityManagerInterface $em
     * @param InvitationManager $invitationManager
     */
    public function __construct(EntityManagerInterface $em, InvitationManager $invitationManager)
    {
        $this->em = $em;
        $this->repo = $em->getRepository(InvitationBlacklist::class);
        $this->invitationManager = $invitationManager;
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
        }
        return;
    }
}
