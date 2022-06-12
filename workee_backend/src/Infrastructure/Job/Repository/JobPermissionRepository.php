<?php

namespace App\Infrastructure\Job\Repository;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use App\Core\Components\Job\Entity\JobPermission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Core\Components\Job\Repository\JobPermissionRepositoryInterface;

/**
 * @method JobPermission|null find($id, $lockMode = null, $lockVersion = null)
 * @method JobPermission|null findOneBy(array $criteria, array $orderBy = null)
 * @method JobPermission[]    findAll()
 * @method JobPermission[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JobPermissionRepository extends ServiceEntityRepository implements JobPermissionRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobPermission::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(JobPermission $entity, bool $flush = true): void
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
    public function remove(JobPermission $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return JobPermission[] Returns an array of JobPermission objects
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

    //find permissions by job
    public function findPermissionsByJob($job): array
    {
        return $this->createQueryBuilder('jp')
            ->select('p')
            ->where('jp.job = :job')
            ->setParameter('job', $job)
            ->leftJoin(
                'App\Core\Components\Job\Entity\Permission',
                'p',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'jp.permission = p.id'
            )
            ->getQuery()
            ->getResult();
    }

    public function findOneById($id): ?JobPermission
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
