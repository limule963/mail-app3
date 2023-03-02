<?php

namespace App\Repository;

use App\Entity\Lead;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lead>
 *
 * @method Lead|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lead|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lead[]    findAll()
 * @method Lead[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LeadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lead::class);
    }

    public function save(Lead $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Lead $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

   /**
    * @return Lead[] Returns an array of Lead objects
    */
   public function findByStatus($compaignId,$status,$n = 1000): array
   {
       return $this->createQueryBuilder('l')
            // ->select('l','s')
            ->andWhere('l.compaign = :val2')
            ->andWhere('s.status = :val')
            ->join('l.status','s')
            ->setParameter('val', $status)
            ->setParameter('val2', $compaignId)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults($n)
            ->getQuery()
            ->getResult()
       ;
   }
   /**
    * @return Lead[] Returns an array of Lead objects
    */
   public function findByCompaignId($id,$number = 10): array
   {
       return $this->createQueryBuilder('l')
            // ->select('l','s')
            ->andWhere('l.compaign = :val')
            // ->join('l.status','s')
            ->setParameter('val', $id)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults($number)
            ->getQuery()
            ->getResult()
       ;
   }
//    /**
//     * @return Lead[] Returns an array of Lead objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

        public function findOneByEmailAddress($compaignId,$emailAddress): ?Lead
        {
            return $this->createQueryBuilder('l')
                ->andWhere('l.compaign = :val2')
                ->andWhere('l.emailAddress = :val3')
                ->setParameter('val2', $compaignId)
                ->setParameter('val3', $emailAddress)
                ->getQuery()
                ->getOneOrNullResult()
            ;
        }
    // public function findOneByStatus($compaignId,$stepLeadStatus): ?Lead
    // {
    //     return $this->createQueryBuilder('l')
    //         // ->select('l','s')
    //         ->andWhere('l.compaign = :val2')
    //         ->andWhere('s.status = :val3')
    //         ->join('l.status','s')
    //         ->setParameter('val2', $compaignId)
    //         ->setParameter('val3', $stepLeadStatus)
    //         ->getQuery()
    //         ->getOneOrNullResult()
    //     ;
    // }


    public function findBySender($compaignId,$stepLeadStatus,$sender, $number = 10)
    {
        $query = $this->createQueryBuilder('l')
            // ->select('l','s')
            ->andWhere('l.compaign = :val2')
            ->andWhere('s.status = :val3')
            ->join('l.status','s')
            ->setParameter('val2', $compaignId)
            ->setParameter('val3', $stepLeadStatus)
            ->setMaxResults($number)
            ;
        if($sender !='')
        {
            $query->andWhere('l.sender = :val')
                ->setParameter('val', $sender);
        }
        
        return $query->getQuery()
                ->getResult();
    }

    // public function findOneByCompaignId($compaignId): ?Lead
    // {
    //     return $this->createQueryBuilder('l')
    //         // ->select('l','s')
    //         ->andWhere('l.compaign = :val2')
    //         ->setParameter('val2', $compaignId)
    //         ->getQuery()
    //         ->getOneOrNullResult()
    //     ;
    // }
}
