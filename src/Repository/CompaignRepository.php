<?php

namespace App\Repository;

use App\Entity\Compaign;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Compaign>
 *
 * @method Compaign|null find($id, $lockMode = null, $lockVersion = null)
 * @method Compaign|null findOneBy(array $criteria, array $orderBy = null)
 * @method Compaign[]    findAll()
 * @method Compaign[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompaignRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Compaign::class);
    }

    public function save(Compaign $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Compaign $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

   /**
    * @return Compaign[] Returns an array of Compaign objects
    */
   public function findByUserId($id): array
   {
       return $this->createQueryBuilder('c')
            ->select('c','s','t, d')
            ->andWhere('c.user = :val')
            // ->innerJoin('c.user','u')
            // ->innerJoin('c.leads','l')
            ->innerJoin('c.steps','s')
            ->innerJoin('c.status','t')
            ->join('c.dsns','d')
            ->setParameter('val', $id)
            ->orderBy('c.id', 'ASC')
            // ->setMaxResults($n)
            ->getQuery()
            ->getResult()
       ;
   }
//    /**
//     * @return Compaign[] Returns an array of Compaign objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Compaign
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
