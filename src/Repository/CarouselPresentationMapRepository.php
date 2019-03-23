<?php

namespace App\Repository;

use App\Entity\CarouselPresentationMap;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CarouselPresentationMap|null find($id, $lockMode = null, $lockVersion = null)
 * @method CarouselPresentationMap|null findOneBy(array $criteria, array $orderBy = null)
 * @method CarouselPresentationMap[]    findAll()
 * @method CarouselPresentationMap[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CarouselPresentationMapRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CarouselPresentationMap::class);
    }

    // /**
    //  * @return CarouselPresentationMap[] Returns an array of CarouselPresentationMap objects
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
    public function findOneBySomeField($value): ?CarouselPresentationMap
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
