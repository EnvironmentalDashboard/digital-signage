<?php

namespace App\Repository;

use App\Entity\Template;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Template|null find($id, $lockMode = null, $lockVersion = null)
 * @method Template|null findOneBy(array $criteria, array $orderBy = null)
 * @method Template[]    findAll()
 * @method Template[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TemplateRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Template::class);
    }

    /**
     * @return Template[] Returns an array of Template objects
     * @todo this and the below method have brittle checks to sort controller vs presentation templates- add extra db column?
     */
    public function findControllerTemplates()
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.style IS NULL')
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Template[] Returns an array of Template objects
     */
    public function findPresentationTemplates()
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.style IS NOT NULL')
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /*
    public function findOneBySomeField($value): ?Template
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
