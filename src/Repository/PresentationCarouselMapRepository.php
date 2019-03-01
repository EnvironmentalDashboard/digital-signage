<?php

namespace App\Repository;

use App\Entity\PresentationCarouselMap;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method PresentationCarouselMap|null find($id, $lockMode = null, $lockVersion = null)
 * @method PresentationCarouselMap|null findOneBy(array $criteria, array $orderBy = null)
 * @method PresentationCarouselMap[]    findAll()
 * @method PresentationCarouselMap[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PresentationCarouselMapRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, PresentationCarouselMap::class);
    }

    // /**
    //  * @return PresentationCarouselMap[] Returns an array of PresentationCarouselMap objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PresentationCarouselMap
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
