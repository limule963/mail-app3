<?php

namespace App\Repository;

use App\Entity\Dsn;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Dsn>
 *
 * @method Dsn|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dsn|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dsn[]    findAll()
 * @method Dsn[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DsnRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dsn::class);
    }

    public function save(Dsn $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Dsn $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
        * @return Dsn[] Returns an array of Dsn objects
        */
    public function findByCompaignId($compaignId,$n = 50): array
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.compaign = :val')
            ->setParameter('val', $compaignId)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults($n)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByUserId($userId,$number = 50 )
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.user = :val')
            ->setParameter('val', $userId)
            ->setMaxResults($number)
            ->getQuery()
            ->getResult()
        ;
    }
}
