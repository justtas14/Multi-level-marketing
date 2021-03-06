<?php


namespace App\Service;

use App\Entity\Associate;
use App\Entity\Invitation;
use App\Entity\User;
use App\Exception\NotAncestorException;
use App\Repository\AssociateRepository;
use Doctrine\Instantiator\Exception\ExceptionInterface;
use Doctrine\ORM\EntityManagerInterface;
use PlumTreeSystems\FileBundle\Service\GaufretteFileManager;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AssociateManager
{
    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    /** @var TokenStorageInterface $tokenStorage */
    private $tokenStorage;

    /**
     * @var AssociateRepository $associateRepository
     */
    private $associateRepository;

    /**
     * @var LoggerInterface $databaseLogger
     */
    private $databaseLogger;

    /**
     * @var GaufretteFileManager $gaufretterFileManager
     */
    private $gaufretteFileManager;

    /**
     * AssociateManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param TokenStorageInterface $tokenStorage
     * @param LoggerInterface $databaseLogger
     * @param GaufretteFileManager $gaufretteFileManager
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TokenStorageInterface $tokenStorage,
        LoggerInterface $databaseLogger,
        GaufretteFileManager $gaufretteFileManager
    ) {
        $this->em = $entityManager;
        $this->tokenStorage = $tokenStorage;
        $this->associateRepository = $this->em->getRepository(Associate::class);
        $this->databaseLogger = $databaseLogger;
        $this->gaufretteFileManager = $gaufretteFileManager;
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

        $associate = $this->associateRepository->findOneBy(['associateId' => $parentAssociateId]);

        $allDirectAssociates = $this->associateRepository->findAllDirectAssociates(
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
        $levels = $this->associateRepository->findMaxLevel();

        if ($associateId) {
            $associate = $this->associateRepository->findOneBy(['associateId' => $associateId]);
            $maxLevel = $this->associateRepository->findMaxLevel($associate->getAncestors().$associate->getId());
            $levels = ($maxLevel)? ($maxLevel - $associate->getLevel()) : 0;
        }

        return $levels;
    }

    public function isAncestor($associateChildId, $parentAssociateId, $searchByAssociateId = true)
    {
        $parentAssociate = $this->associateRepository->findOneBy([
            ($searchByAssociateId ? 'associateId' : 'id') => $parentAssociateId
        ]);

        $childAssociate = $this->associateRepository->findOneBy([
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
        if (!$override) {
            $token = $this->tokenStorage->getToken();

            /** @var User $user */
            $user = $token->getUser();

            if (!in_array('ROLE_ADMIN', $user->getRoles()) &&
                !$this->isAncestor($id, $user->getAssociate()->getId(), false)) {
                throw new NotAncestorException('The target associate, is not in user\'s downline');
            }
        }
        return $this->associateRepository->findOneBy(['id' => $id]);
    }

    /**
     * @param string $associateId
     * @param int $level
     * Return number of associates in level of given associate's downline
     * @return int
     */
    public function getNumberOfAssociatesInDownline(int $level, string $associateId = null) : int
    {
        $associate = $this->associateRepository->findOneBy(['associateId' => $associateId]);

        $currentAncestor = null;
        $levelToBeginWith = 0;
        if ($associate) {
            $currentAncestor = $associate->getAncestors().$associate->getId();
            $levelToBeginWith = $associate->getLevel();
        }

        $numberOfAssociates = $this->associateRepository->findAssociatesByLevel(
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
        /** @var Associate $associate */
        $associate = $user->getAssociate();

        $userAssociateId = ($associate)?($associate->getId()):(-1);

        if (!$parentId && $associate) {
            return [
                'id' => $userAssociateId,
                'title' => $user->getAssociate()->getFullName(),
                'parentId' => $user->getAssociate()->getParentId(),
                'numberOfChildren' => $this->associateRepository->findAllDirectAssociatesCount($userAssociateId),
                'filePath' => $associate->getProfilePicture() ?
                    $this->gaufretteFileManager->generateDownloadUrl($associate->getProfilePicture()) : ''
            ];
        } elseif (!$user->isAdmin() && !$this->isAncestor($parentId, $userAssociateId, false)) {
            throw new NotAncestorException(
                'Attempted to get direct downline of an associate that is not in your downline'
            );
        }
        $directAssociates = $this->associateRepository->findAllDirectAssociates($parentId);

        $associateRepo = $this->associateRepository;

        return array_map(
            function ($downlineNode) use ($associateRepo, $parentId) {
                $thisAssociate = $associateRepo->find($downlineNode['id']);
                return [
                    'id' => $downlineNode['id'],
                    'title' => $downlineNode['fullName'],
                    'parentId' => $parentId,
                    'numberOfChildren' => $associateRepo->findAllDirectAssociatesCount($downlineNode['id']),
                    'filePath' => $thisAssociate->getProfilePicture() ?
                        $this->gaufretteFileManager->generateDownloadUrl($thisAssociate->getProfilePicture()) : ''
                ];
            },
            $directAssociates
        );
    }

    /**
     * @param $associate
     * @return mixed
     */
    public function getAllAssociateChildren(Associate $associate)
    {
        $associateId = $associate->getId();
        $currentAssociateAncestor = $associate->getAncestors();
        $childrenAncestorsFilter = $currentAssociateAncestor.$associateId.'|';
        $associates = $this->associateRepository->findAllAssociateChildren($childrenAncestorsFilter);
        return $associates;
    }

    public function changeAssociateParent($associateId, $associateParentId)
    {
        $currentAssociate = $this->getAssociate($associateId);
        $parentAssociate = $this->getAssociate($associateParentId);

        $initialLevel = $currentAssociate->getLevel();

        $currentAssociateAncestor = $currentAssociate->getAncestors();
        $parentAssociateAncestor = $parentAssociate->getAncestors();

        $associates = $this->getAllAssociateChildren($currentAssociate);

        $currentAssociate->setParentId($associateParentId);
        $currentAssociate->setParent($parentAssociate);

        $this->em->persist($currentAssociate);

        $parentAssociateAncestorChildren = $parentAssociateAncestor.$associateParentId.'|';

        $currentAssociate->setAncestors($parentAssociateAncestorChildren);

        /** @var Associate $associate */
        foreach ($associates as $associate) {
            $associate->setAncestors(
                str_replace(
                    $currentAssociateAncestor,
                    $parentAssociateAncestorChildren,
                    $associate->getAncestors()
                )
            );
            $associate->setLevel(($associate->getLevel() + ($currentAssociate->getLevel() - $initialLevel)));

            $this->em->persist($associate);
        }

        $this->databaseLogger->info(
            'Successfully changed '.$currentAssociate->getFullName().' associate parent'.
            'to '.$parentAssociate->getFullName()
        );

        $this->em->flush();
    }

    /**
     * @param $deleteAssociate
     * @return bool
     * @throws \Exception
     */
    public function deleteAssociate(Associate $deleteAssociate)
    {
        $childrenAncestorFilter = $deleteAssociate->getAncestors().$deleteAssociate->getId().'|';
        $childrenCount = $this->associateRepository->findAssociateChildren($childrenAncestorFilter);

        if ($childrenCount === 0) {
            $user = $this->em->getRepository(User::class)->findOneBy(['associate' => $deleteAssociate]);
            $user->setAssociate(null);
            $invitations = $this->em->getRepository(Invitation::class)->findBy(['sender' => $deleteAssociate]);
            foreach ($invitations as $invitation) {
                $this->em->remove($invitation);
            }

            $deleteAssociateFullName = $deleteAssociate->getFullName();

            $this->em->persist($user);
            $this->em->remove($deleteAssociate);
            $this->em->flush();

            $this->databaseLogger->info('Successfully deleted '.$deleteAssociateFullName.' associate');

            return true;
        } else {
            return false;
        }
    }

    /**
     * @param User $user
     * @throws \Exception
     */
    public function deleteUser(User $user)
    {
        $userAssociate = $user->getAssociate();
        $userEmail = $user->getEmail();
        if ($userAssociate) {
            $user->setAssociate(null);
            $this->em->persist($user);
            $this->em->remove($userAssociate);
        }
        $this->em->remove($user);
        $this->em->flush();

        $this->databaseLogger->info('Successfully deleted user with email '.$userEmail);
    }

    public function createUniqueUserNameInvitation($fullName) : string
    {
        $fullName = strtolower($fullName);
        $fullName = preg_replace('/[0-9]+/', '', $fullName);
        $fullName = trim($fullName);
        $fullName = preg_replace('/\s+/', ' ', $fullName);
        $fullName = preg_replace('/\s/', '-', $fullName);


        $randomSequence = rand(0, 9).rand(0, 9).rand(0, 9);
        if ($this->findByUserName($fullName)) {
            $fullName = $fullName.$randomSequence;
        }

        return $fullName;
    }

    public function findByUserName($userName) : ?Associate
    {
        $associate = $this->associateRepository->findOneBy(['uniqueUserName' => $userName]);
        if (!$associate) {
            return null;
        }
        return $associate;
    }
}
