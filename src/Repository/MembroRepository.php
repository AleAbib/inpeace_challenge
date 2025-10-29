<?php

namespace App\Repository;

use App\Entity\Membro;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Membro>
 *
 * @method Membro|null find($id, $lockMode = null, $lockVersion = null)
 * @method Membro|null findOneBy(array $criteria, array $orderBy = null)
 * @method Membro[]    findAll()
 * @method Membro[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MembroRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Membro::class);
    }

//    /**
//     * @return Membro[] Returns an array of Membro objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Membro
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
