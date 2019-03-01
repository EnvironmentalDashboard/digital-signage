<?php

namespace App\Repository;

use App\Entity\RemoteController;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method RemoteController|null find($id, $lockMode = null, $lockVersion = null)
 * @method RemoteController|null findOneBy(array $criteria, array $orderBy = null)
 * @method RemoteController[]    findAll()
 * @method RemoteController[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RemoteControllerRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, RemoteController::class);
    }

    // /**
    //  * @return RemoteController[] Returns an array of RemoteController objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RemoteController
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
