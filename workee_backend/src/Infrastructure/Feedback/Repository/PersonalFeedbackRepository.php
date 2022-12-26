<?php

namespace App\Infrastructure\Feedback\Repository;

use Doctrine\ORM\ORMException;
use App\Core\Components\User\Entity\User;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use App\Core\Components\User\Service\GetUserService;
use App\Core\Components\Feedback\Entity\PersonalFeedback;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Core\Components\Feedback\Repository\PersonalFeedbackRepositoryInterface;

class PersonalFeedbackRepository extends ServiceEntityRepository implements PersonalFeedbackRepositoryInterface
{
    public function __construct(private ManagerRegistry $registry, private GetUserService $getUserService)
    {
        parent::__construct($registry, PersonalFeedback::class);
    }

    /**
    * @throws ORMException
    * @throws OptimisticLockException
    */
    public function add(PersonalFeedback $entity, bool $flush = true): void
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
    public function remove(PersonalFeedback $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }
    /**
     * @param mixed $id
     * @return \App\Core\Components\Feedback\Entity\PersonalFeedback|null
     */
    public function findOneById($id): ?PersonalFeedback
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     *
     * @param User $receiver
     * @param int $limit
     * @return array
     */
    public function findByReceiver(User $receiver, int $limit): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.receiver = :receiver')
            ->setParameter('receiver', $receiver)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }
}
