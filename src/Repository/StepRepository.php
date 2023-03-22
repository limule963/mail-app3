<?php

namespace App\Repository;

use App\Entity\Step;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Step>
 *
 * @method Step|null find($id, $lockMode = null, $lockVersion = null)
 * @method Step|null findOneBy(array $criteria, array $orderBy = null)
 * @method Step[]    findAll()
 * @method Step[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StepRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Step::class);
    }

    public function save(Step $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Step $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

   /**
    * @return Step[] Returns an array of Step objects
    */
   public function findByCompaignId($id,$order = 'ASC'): array
   {
       return $this->createQueryBuilder('s')
            // ->select('s','e','t')
            ->andWhere('s.compaign = :val')
            // ->join('s.email','e')
            // ->join('s.status','t')
            ->setParameter('val', $id)
            ->orderBy('s.id', $order)
        //     ->setMaxResults(10)
            ->getQuery()
            ->getResult()
       ;
   }
//    /**
//     * @return Step[] Returns an array of Step objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    public function findOneByCompaignId($compaignId): ?Step
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $compaignId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    public function findOneByStepOrder($compaignId,int $stepOrder): ?Step
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.compaign = :val')
            ->andWhere('s.stepOrder = :val2')
            ->setParameter('val', $compaignId)
            ->setParameter('val2', $stepOrder)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
