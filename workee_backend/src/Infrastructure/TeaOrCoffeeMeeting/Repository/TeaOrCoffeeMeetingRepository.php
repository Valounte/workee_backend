<?php

namespace App\Infrastructure\TeaOrCoffeeMeeting\Repository;

use App\Client\ViewModel\TeaOrCoffeeMeeting\TeaOrCoffeeMeetingViewModel;
use Doctrine\ORM\ORMException;
use App\Core\Components\User\Entity\User;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use App\Core\Components\TeaOrCoffeeMeeting\Entity\TeaOrCoffeeMeeting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Core\Components\TeaOrCoffeeMeeting\Repository\TeaOrCoffeeMeetingRepositoryInterface;

/**
 * @method TeaOrCoffeeMeeting|null find($id, $lockMode = null, $lockVersion = null)
 * @method TeaOrCoffeeMeeting|null findOneBy(array $criteria, array $orderBy = null)
 * @method TeaOrCoffeeMeeting[]    findAll()
 * @method TeaOrCoffeeMeeting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TeaOrCoffeeMeetingRepository extends ServiceEntityRepository implements TeaOrCoffeeMeetingRepositoryInterface
{
    public function __construct(
        private ManagerRegistry $registry,
        private TeaOrCoffeeMeetingUserRepository $teaOrCoffeeMeetingUserRepository,
    ) {
        parent::__construct($registry, TeaOrCoffeeMeeting::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(TeaOrCoffeeMeeting $entity, bool $flush = true): void
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
    public function remove(TeaOrCoffeeMeeting $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return TeaOrCoffeeMeeting[] Returns an array of TeaOrCoffeeMeeting objects
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

    public function findTeaOrCoffeeByUser($user): ?array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.user = :val')
            ->andWhere('t.invitationStatus = :status')
            ->setParameter('status', 'PENDING')
            ->setParameter('val', $user)
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    // public function findMeetingsWhereUserIsInitiation(User $user): ?array
    // {
    //     $maxDate = new \DateTime();

    //     $rawResult = $this->createQueryBuilder('t')
    //         ->andWhere('t.initiator = :val')
    //         ->andWhere('t.date > :maxDate')
    //         ->setParameter('maxDate', $maxDate)
    //         ->setParameter('val', $user)
    //         ->orderBy('t.id', 'ASC')
    //         ->getQuery()
    //         ->getResult()
    //     ;

    //     $result = [];
    //     foreach ($rawResult as $teaOrCoffeeMeeting) {

    //     }
    // }
}
