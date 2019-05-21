<?php


namespace App\Service;

use App\Entity\Associate;
use Doctrine\ORM\EntityManager;

class AssociateManager
{
    /**
     * @var EntityManager $em
     */
    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param $parentAssociateId
     * Returns all direct associates by given parent id.
     * Restrictions: this method can be used only by admins or associates when parent id is their id or id in their
     * downline
     *
     * @return Associate[]
     */
    public function getAllDirectAssociates(string $parentAssociateId): array
    {
        $allDirectAssociates = $this->em->getRepository(Associate::class)
            ->findDirectAssociates($parentAssociateId);
    }

    /**
     * @param $associateId
     * Return number of levels based on given associate or from top if null given
     *
     */
    public function getNumberOfLevels(?string $associateId)
    {
        $associate = $this->em->getRepository(Associate::class)->findOneBy(['associateId' => $associateId]);
        return $associate->getLevel();
    }

    /**
     * @param string $associateId
     * @param int $level
     * Return number of associates in level of given associate's downline
     */
    public function getNumberOfAssociatesInDownline(string $associateId, int $level)
    {

    }




}