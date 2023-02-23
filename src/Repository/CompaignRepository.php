<?php

namespace App\Repository;

use App\Entity\Compaign;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\Compiler\Compiler;

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
    * @return Compaign Returns null or  Compaign object
    */
   public function findOneById($id):?Compaign
   {
       return $this->createQueryBuilder('c')
            ->andWhere('c.id = :val')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult()
            
       ;
   }

   /**
    * @return Compaign Returns null or  Compaign object
    */
   public function findOneWithData($id):?Compaign
   {
       return $this->createQueryBuilder('c')
            ->select('c','s','t, d','sh')
            ->andWhere('c.id = :val')
            ->join('c.steps','s')
            ->join('c.status','t')
            ->join('c.dsns','d')
            ->join('c.schedule','sh')
            ->setParameter('val', $id)
            ->getQuery()
            ->getOneOrNullResult()
            
       ;
   }
    /**
        * @return Compaign[] Returns an array of Compaign objects
        */
    public function findByUserId($id,$number = 1000): array
    {
        
            return $this->createQueryBuilder('c')
                        ->andWhere('c.user = :val')
                        ->setParameter('val', $id)
                        ->orderBy('c.id', 'ASC')
                        ->setMaxResults($number)
                        ->getQuery()
                        ->getResult()
        ;
    }
    /**
        * @return Compaign[] Returns an array of Compaign objects
        */
    public function findWithDataByUserId($id,$number = 1000): array
    {
        
            return $this->createQueryBuilder('c')
                        ->select('c','s','t, d','sh')
                        ->andWhere('c.user = :val')
                        ->join('c.steps','s')
                        ->join('c.status','t')
                        ->join('c.dsns','d')
                        ->join('c.schedule','sh')
                        ->setParameter('val', $id)
                        ->orderBy('c.id', 'ASC')
                        ->setMaxResults($number)
                        ->getQuery()
                        ->getResult()
        ;
    }

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
