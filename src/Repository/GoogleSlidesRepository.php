<?php

namespace App\Repository;

use App\Entity\GoogleSlides;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method GoogleSlides|null find($id, $lockMode = null, $lockVersion = null)
 * @method GoogleSlides|null findOneBy(array $criteria, array $orderBy = null)
 * @method GoogleSlides[]    findAll()
 * @method GoogleSlides[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GoogleSlidesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, GoogleSlides::class);
    }

    // /**
    //  * @return GoogleSlides[] Returns an array of GoogleSlides objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?GoogleSlides
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
