<?php

namespace App\Repository;

use App\Entity\Enjeu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Enjeu|null find($id, $lockMode = null, $lockVersion = null)
 * @method Enjeu|null findOneBy(array $criteria, array $orderBy = null)
 * @method Enjeu[]    findAll()
 * @method Enjeu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnjeuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Enjeu::class);
    }

    // /**
    //  * @return Enjeu[] Returns an array of Enjeu objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Enjeu
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
