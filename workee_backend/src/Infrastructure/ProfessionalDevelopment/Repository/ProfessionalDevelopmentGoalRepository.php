<?php

namespace App\Infrastructure\ProfessionalDevelopment\Repository;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Core\Components\ProfessionalDevelopment\Entity\ProfessionalDevelopmentGoal;
use App\Core\Components\ProfessionalDevelopment\Repository\ProfessionalDevelopmentGoalRepositoryInterface;

/**
 * @method ProfessionalDevelopmentGoal|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProfessionalDevelopmentGoal|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProfessionalDevelopmentGoal[]    findAll()
 * @method ProfessionalDevelopmentGoal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfessionalDevelopmentGoalRepository extends ServiceEntityRepository implements ProfessionalDevelopmentGoalRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProfessionalDevelopmentGoal::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(ProfessionalDevelopmentGoal $entity, bool $flush = true): void
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
    public function remove(ProfessionalDevelopmentGoal $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function get(int $id): ProfessionalDevelopmentGoal
    {
        return $this->find($id);
    }
}
