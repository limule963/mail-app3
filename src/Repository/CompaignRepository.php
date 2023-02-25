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
   public function findOneByUserId($id):?Compaign
   {
       return $this->createQueryBuilder('c')
            ->andWhere('c.user = :val')
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

    public function findOneByName($name): ?Compaign
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.name = :val')
            ->setParameter('val', $name)
            ->getQuery()
            ->getOneOrNullResult()
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
