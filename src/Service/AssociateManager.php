<?php


namespace App\Service;

use App\Entity\Associate;
use App\Entity\User;
use App\Exception\GetAllDirectAssociatesException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AssociateManager
{
    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    /** @var TokenStorageInterface $tokenStorage */
    private $tokenStorage;

    public function __construct(EntityManagerInterface $entityManager, TokenStorageInterface $tokenStorage)
    {
        $this->em = $entityManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param $parentAssociateId
     * Returns all direct associates by given parent id.
     * Restrictions: this method can be used only by admins or associates when parent id is their id or id in their
     * downline
     *
     * @return Associate[]
     * @throws \Exception
     */
    public function getAllDirectAssociates(string $parentAssociateId): array
    {
        $token = $this->tokenStorage->getToken();

        /** @var User $user */
        $user = $token->getUser();

        if (!$this->isAncestor($parentAssociateId, $user->getAssociate()->getAssociateId())
            && !in_array('ROLE_ADMIN', $user->getRoles())) {
            throw new GetAllDirectAssociatesException('Given parent is not in user downline');
        }

        $associateRepository = $this->em->getRepository(Associate::class);

        $associate = $associateRepository->findOneBy(['associateId' => $parentAssociateId]);

        $allDirectAssociates = $associateRepository->findAllDirectAssociates(
            $associate->getId()
        );

        return $allDirectAssociates;
    }

    /**
     * @param $associateId
     * Return number of levels based on given associate or from top if null given
     * @return int
     */
    public function getNumberOfLevels(?string $associateId = null) : int
    {
        $associateRepository = $this->em->getRepository(Associate::class);
        $levels = $associateRepository->findMaxLevel();

        if ($associateId) {
            $associate = $associateRepository->findOneBy(['associateId' => $associateId]);
            $maxLevel = $associateRepository->findMaxLevel($associate->getAncestors().$associate->getId());
            $levels = ($maxLevel)? ($maxLevel - $associate->getLevel()) : 0;
        }

        return $levels;
    }

    public function isAncestor($associateChildId, $parentAssociateId)
    {
        $associateRepository = $this->em->getRepository(Associate::class);

        $parentAssociate = $associateRepository->findOneBy(['associateId' => $parentAssociateId]);

        $childAssociate = $associateRepository->findOneBy(['associateId' => $associateChildId]);

        $ancestor = $parentAssociate->getAncestors().$parentAssociate->getId();

        if (substr($childAssociate->getAncestors().$childAssociate->getId(), 0, strlen($ancestor))
            === $ancestor) {
            return true;
        }
        return false;
    }

    /**
     * @param string $associateId
     * @param int $level
     * Return number of associates in level of given associate's downline
     * @return int
     */
    public function getNumberOfAssociatesInDownline(int $level, string $associateId = null) : int
    {
        $associateRepository = $this->em->getRepository(Associate::class);

        $associate = $associateRepository->findOneBy(['associateId' => $associateId]);

        $currentAncestor = null;
        $levelToBeginWith = 0;
        if ($associate) {
            $currentAncestor = $associate->getAncestors().$associate->getId();
            $levelToBeginWith = $associate->getLevel();
        }

        $numberOfAssociates = $associateRepository->findAssociatesByLevel(
            $level + $levelToBeginWith,
            $currentAncestor
        );

        return $numberOfAssociates;
    }
}
