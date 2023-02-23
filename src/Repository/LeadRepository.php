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
   public function findByStatus($status,$n = 1000): array
   {
       return $this->createQueryBuilder('l')
            ->select('l','s')
            ->andWhere('s.status = :val')
            ->join('l.status','s')
            ->setParameter('val', $status)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults($n)
            ->getQuery()
            ->getResult()
       ;
   }
   /**
    * @return Lead[] Returns an array of Lead objects
    */
   public function findByCompainId($id,$number = 10): array
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

    public function findOneBySender($compaignId,$stepLeadStatus,$sender): ?Lead
    {
        return $this->createQueryBuilder('l')
            ->select('l','s')
            ->andWhere('l.compaign = :val2')
            ->andWhere('l.sender = :val')
            ->andWhere('s.status = :val3')
            ->join('l.status','s')
            ->setParameter('val', $sender)
            ->setParameter('val2', $compaignId)
            ->setParameter('val3', $stepLeadStatus)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    public function findOne($compaignId): ?Lead
    {
        return $this->createQueryBuilder('l')
            ->select('l','s')
            ->andWhere('l.compaign = :val2')
            ->setParameter('val2', $compaignId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
