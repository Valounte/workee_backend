<?php

namespace App\Infrastructure\ProfessionalDevelopment\Repository;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Core\Components\ProfessionalDevelopment\Entity\ProfessionalDevelopmentSubGoal;
use App\Core\Components\ProfessionalDevelopment\Repository\ProfessionalDevelopmentSubGoalRepositoryInterface;

/**
 * @method ProfessionalDevelopmentSubGoal|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProfessionalDevelopmentSubGoal|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProfessionalDevelopmentSubGoal[]    findAll()
 * @method ProfessionalDevelopmentSubGoal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfessionalDevelopmentSubGoalRepository extends ServiceEntityRepository implements ProfessionalDevelopmentSubGoalRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProfessionalDevelopmentSubGoal::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(ProfessionalDevelopmentSubGoal $entity, bool $flush = true): void
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
    public function remove(ProfessionalDevelopmentSubGoal $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
}
