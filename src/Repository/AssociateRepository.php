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
        return $this->createQueryBuilder('a')
            ->where('a.fullName LIKE :fullName')
            ->andWhere('a.email LIKE :email')
            ->andWhere('a.mobilePhone LIKE :telephone')
            ->setParameter('fullName', '%'.$filter->getFullName().'%')
            ->setParameter('email', '%'.$filter->getEmail().'%')
            ->setParameter('telephone', '%'.$filter->getTelephone().'%');
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
        if (!$ancestors) {
            $ancestors = '|';
        }
        return $this->createQueryBuilder('a')
            ->select('MAX(a.level)')
            ->where('a.ancestors LIKE :ancestors')
            ->setParameter('ancestors', $ancestors.'%')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findAssociatesByLevel($level, $currentAncestor = null) : int
    {
        if (!$currentAncestor) {
            $currentAncestor = '|';
        }
        return $this->createQueryBuilder('a')
            ->select('COUNT(a)')
            ->where('a.level = :level')
            ->andWhere('a.ancestors LIKE :ancestors')
            ->setParameter('level', $level)
            ->setParameter('ancestors', $currentAncestor.'%')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findAllDirectAssociates($parentId) : array
    {
        return $this->createQueryBuilder('a')
            ->where('a.parentId = :parentId')
            ->setParameter('parentId', $parentId)
            ->getQuery()
            ->getArrayResult();
    }


    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
