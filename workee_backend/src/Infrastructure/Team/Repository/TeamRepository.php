<?php

namespace App\Infrastructure\Team\Repository;

use Doctrine\ORM\ORMException;
use App\Core\Components\Team\Entity\Team;
use App\Core\Components\Team\Repository\TeamRepositoryInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Team|null find($id, $lockMode = null, $lockVersion = null)
 * @method Team|null findOneBy(array $criteria, array $orderBy = null)
 * @method Team[]    findAll()
 * @method Team[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TeamRepository extends ServiceEntityRepository implements TeamRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Team::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Team $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Team $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return Team[] Returns an array of Team objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    public function findOneById($id): ?Team
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    //find teams by company
    public function findTeamsByCompany($companyId): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.company = :companyId')
            ->setParameter('companyId', $companyId)
            ->getQuery()
            ->getResult()
        ;
    }
}
