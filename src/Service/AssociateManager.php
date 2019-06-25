<?php


namespace App\Service;

use App\Entity\Associate;
use App\Entity\User;
use App\Exception\NotAncestorException;
use App\Repository\AssociateRepository;
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
            throw new NotAncestorException('Given parent is not in user downline');
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

    public function isAncestor($associateChildId, $parentAssociateId, $searchByAssociateId = true)
    {
        $associateRepository = $this->em->getRepository(Associate::class);

        $parentAssociate = $associateRepository->findOneBy([
            ($searchByAssociateId ? 'associateId' : 'id') => $parentAssociateId
        ]);

        $childAssociate = $associateRepository->findOneBy([
            ($searchByAssociateId ? 'associateId' : 'id') => $associateChildId
        ]);
        if (!$childAssociate) {
            return false;
        }

        $ancestor = $parentAssociate->getAncestors().$parentAssociate->getId();

        if (substr($childAssociate->getAncestors().$childAssociate->getId(), 0, strlen($ancestor))
            === $ancestor) {
            return true;
        }
        return false;
    }

    public function getAssociate($id, $override = false)
    {
        $associateRepository = $this->em->getRepository(Associate::class);

        if (!$override) {
            $token = $this->tokenStorage->getToken();

            /** @var User $user */
            $user = $token->getUser();

            if (!in_array('ROLE_ADMIN', $user->getRoles()) &&
                !$this->isAncestor($id, $user->getAssociate()->getId(), false)) {
                throw new NotAncestorException('The target associate, is not in user\'s downline');
            }
        }
        return $associateRepository->findOneBy(['id' => $id]);
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

    /**
     * @param null $parentId
     * @return array
     * @throws NotAncestorException
     */
    public function getDirectDownlineAssociates($parentId = null)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $associate = $user->getAssociate();

        $userAssociateId = ($associate)?($associate->getId()):(-1);

        /** @var AssociateRepository $associateRepo */
        $associateRepo = $this->em->getRepository(Associate::class);
        if (!$parentId && $associate) {
            return [
                'id' => $userAssociateId,
                'title' => $user->getAssociate()->getFullName(),
                'parentId' => $user->getAssociate()->getParentId(),
                'numberOfChildren' => $associateRepo->findAllDirectAssociatesCount($userAssociateId)
            ];
        } elseif (!$user->isAdmin() && !$this->isAncestor($parentId, $userAssociateId, false)) {
            throw new NotAncestorException(
                'Attempted to get direct downline of an associate that is not in your downline'
            );
        }
        $directAssociates = $associateRepo->findAllDirectAssociates($parentId);
        return array_map(
            function ($downlineNode) use ($associateRepo, $parentId) {
                return [
                    'id' => $downlineNode['id'],
                    'title' => $downlineNode['fullName'],
                    'parentId' => $parentId,
                    'numberOfChildren' => $associateRepo->findAllDirectAssociatesCount($downlineNode['id'])
                ];
            },
            $directAssociates
        );
    }
}
