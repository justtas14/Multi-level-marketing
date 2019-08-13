<?php

namespace App\Repository;

use App\Entity\Gallery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Gallery|null find($id, $lockMode = null, $lockVersion = null)
 * @method Gallery|null findOneBy(array $criteria, array $orderBy = null)
 * @method Gallery[]    findAll()
 * @method Gallery[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GalleryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Gallery::class);
    }

    public function countAllFiles()
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countAllImages($mimeTypes)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('COUNT(c)');

        return $qb->add('where', $qb->expr()->in('c.mimeType', ':mimeTypes'))
            ->setParameter('mimeTypes', $mimeTypes)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countAllNotImages($mimeTypes)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('COUNT(c)');

        return $qb->add('where', $qb->expr()->notIn('c.mimeType', ':mimeTypes'))
            ->setParameter('mimeTypes', $mimeTypes)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findByImages($mimeTypes, $imageLimit, $imageOffset)
    {
        $qb = $this->createQueryBuilder('c');

        return $qb->add('where', $qb->expr()->in('c.mimeType', ':mimeTypes'))
            ->setParameter('mimeTypes', $mimeTypes)
            ->setMaxResults($imageLimit)
            ->setFirstResult($imageOffset)
            ->orderBy('c.created', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByNotImages($mimeTypes, $imageLimit, $imageOffset)
    {
        $qb = $this->createQueryBuilder('c');

        return $qb->add('where', $qb->expr()->notIn('c.mimeType', ':mimeTypes'))
            ->setParameter('mimeTypes', $mimeTypes)
            ->setMaxResults($imageLimit)
            ->setFirstResult($imageOffset)
            ->orderBy('c.created', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Configuration[] Returns an array of Configuration objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Configuration
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
