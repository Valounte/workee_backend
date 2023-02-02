<?php

namespace App\Infrastructure\ProfessionalDevelopment\Repository;

use App\Client\ViewModel\ProfessionalDevelopment\SubGoalViewModel;
use App\Core\Components\ProfessionalDevelopment\Entity\ProfessionalDevelopmentGoal;
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

    public function getSubGoalsByGoal(ProfessionalDevelopmentGoal $goal): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.goal = :goal')
            ->setParameter('goal', $goal)
            ->getQuery()
            ->getResult();
    }

    public function getSubGoalsViewModelsByGoal(ProfessionalDevelopmentGoal $goal): array
    {
        $rawSubGoals = $this->createQueryBuilder('c')
            ->andWhere('c.goal = :goal')
            ->setParameter('goal', $goal)
            ->getQuery()
            ->getResult();

        $subGoalViewModels = [];
        foreach ($rawSubGoals as $rawSubGoal) {
            $subGoalViewModels[] = new SubGoalViewModel(
                $rawSubGoal->getId(),
                $rawSubGoal->getSubGoal(),
                $rawSubGoal->getSubGoalStatus(),
            );
        }

        return $subGoalViewModels;
    }

    public function get(int $id): ProfessionalDevelopmentSubGoal
    {
        return $this->find($id);
    }
}
