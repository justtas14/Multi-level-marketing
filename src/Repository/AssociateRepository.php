<?php

namespace App\Repository;

use App\Entity\Associate;
use App\Filter\AssociateFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Associate|null find($id, $lockMode = null, $lockVersion = null)
 * @method Associate|null findOneBy(array $criteria, array $orderBy = null)
 * @method Associate[]    findAll()
 * @method Associate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssociateRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Associate::class);
    }

    /**
     * @param AssociateFilter $filter
     * @return mixed
     */
    private function getAssociateFilterQuerryBuilder(AssociateFilter $filter) : QueryBuilder
    {
        $qb = $this->createQueryBuilder('a');
        if ($filter->getFullName()) {
            $qb->where('a.fullName LIKE :fullName')
                ->setParameter('fullName', '%'.$filter->getFullName().'%');
        }
        if ($filter->getEmail()) {
            $qb->andWhere('a.email LIKE :email')
                ->setParameter('email', '%'.$filter->getEmail().'%');
        }
        if ($filter->getTelephone()) {
            $qb->andWhere('a.mobilePhone LIKE :telephone')
                ->setParameter('telephone', '%'.$filter->getTelephone().'%');
        }
        return $qb;
    }

    public function findAssociatesByFilter(AssociateFilter $filter, $limit, $offset)
    {
        return $this->getAssociateFilterQuerryBuilder($filter)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }

    public function findAssociatesFilterCount(AssociateFilter $filter) : int
    {
        return $this->getAssociateFilterQuerryBuilder($filter)
            ->select('COUNT(a)')
            ->getQuery()
            ->getSingleScalarResult();
    }


    public function findMaxLevel($ancestors = null) : ?int
    {
        $qb = $this->createQueryBuilder('a')
            ->select('MAX(a.level)');

        if ($ancestors) {
            $qb->where('a.ancestors LIKE :ancestors')
                ->setParameter('ancestors', $ancestors.'%');
        }

        return $qb->getQuery()
            ->getSingleScalarResult();
    }

    public function findAssociatesByLevel($level, $currentAncestor = null) : int
    {
        $qb = $this->createQueryBuilder('a')
            ->select('COUNT(a)')
            ->where('a.level = :level')
            ->andWhere()
            ->setParameter('level', $level);

        if ($currentAncestor) {
            $qb->andWhere('a.ancestors LIKE :ancestors')
                ->setParameter('ancestors', $currentAncestor.'%');
        }
        return $qb->getQuery()
            ->getSingleScalarResult();
    }

    public function findAssociateChildren($currentAncestor) : int
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a)')
            ->andWhere('a.ancestors LIKE :ancestors')
            ->setParameter('ancestors', $currentAncestor.'%')
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function buildDirectAssociateQuery($parentId)
    {
        return $this->createQueryBuilder('a')
            ->where('a.parentId = :parentId')
            ->setParameter('parentId', $parentId)
            ->orderBy('a.joinDate', 'DESC');
    }

    public function findAllDirectAssociates($parentId) : array
    {
        return $this->buildDirectAssociateQuery($parentId)
            ->getQuery()
            ->getArrayResult();
    }

    public function findAllDirectAssociatesCount($parentId)
    {
        return $this->buildDirectAssociateQuery($parentId)
            ->select('COUNT(a)')
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function findAllAssociateChildren($childrenAncestorsFilter)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.ancestors LIKE :ancestors')
            ->setParameter('ancestors', $childrenAncestorsFilter.'%')
            ->getQuery()
            ->getResult();
    }
}
