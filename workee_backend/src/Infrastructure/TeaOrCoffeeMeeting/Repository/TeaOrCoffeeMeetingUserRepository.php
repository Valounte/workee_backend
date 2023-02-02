<?php

namespace App\Infrastructure\TeaOrCoffeeMeeting\Repository;

use DateTime;
use DateInterval;
use Doctrine\ORM\ORMException;
use App\Core\Components\User\Entity\User;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use App\Core\Components\User\Service\GetUserService;
use App\Core\Components\User\Repository\UserRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use App\Client\ViewModel\TeaOrCoffeeMeeting\TeaOrCoffeeMeetingViewModel;
use App\Core\Components\TeaOrCoffeeMeeting\Entity\TeaOrCoffeeMeetingUser;
use App\Client\ViewModel\TeaOrCoffeeMeeting\TeaOrCoffeeMeetingUserViewModel;
use App\Core\Components\TeaOrCoffeeMeeting\Entity\Enum\InvitationStatusEnum;
use App\Client\ViewModel\TeaOrCoffeeMeeting\TeaOrCoffeeMeetingInvitedUserViewModel;
use App\Core\Components\TeaOrCoffeeMeeting\Repository\TeaOrCoffeeMeetingUserRepositoryInterface;

/**
 * @method TeaOrCoffeeMeetingUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method TeaOrCoffeeMeetingUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method TeaOrCoffeeMeetingUser[]    findAll()
 * @method TeaOrCoffeeMeetingUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TeaOrCoffeeMeetingUserRepository extends ServiceEntityRepository implements TeaOrCoffeeMeetingUserRepositoryInterface
{
    public function __construct(
        private ManagerRegistry $registry,
        private UserRepositoryInterface $userRepository,
        private GetUserService $getUserService,
    ) {
        parent::__construct($registry, TeaOrCoffeeMeetingUser::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(TeaOrCoffeeMeetingUser $entity, bool $flush = true): void
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
    public function remove(TeaOrCoffeeMeetingUser $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findById(int $id): ?TeaOrCoffeeMeetingUser
    {
        return $this->find($id);
    }

    public function getAllTeaOrCoffeeMeetingByInitiator(User $user): ?array
    {
        $actualDate = new DateTime();

        $rawResult = $this->createQueryBuilder('t')
            ->leftJoin(
                'App\Core\Components\TeaOrCoffeeMeeting\Entity\TeaOrCoffeeMeeting',
                'u',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                't.meeting = u.id'
            )
            ->select('u')
            ->andWhere('u.initiator = :user')
            ->andWhere('u.date > :actualDate')
            ->setParameter('actualDate', $actualDate)
            ->setParameter('user', $user)
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        $result = [];
        foreach ($rawResult as $item) {
            $user = $this->userRepository->findUserById($item->getInitiator()->getId());
            $result[] = new TeaOrCoffeeMeetingViewModel(
                new TeaOrCoffeeMeetingUserViewModel(
                    $user->getId(),
                    $user->getFirstName(),
                    $user->getLastName(),
                ),
                $this->getInvitedUsersByMeetingId($item->getId()),
                $item->getMeetingType(),
                $item->getDate(),
            );
        }

        return $result;
    }

    public function getAllTeaOrCoffeeMeetingByUser(User $user, InvitationStatusEnum $status): ?array
    {
        $actualDate = new DateTime();
        $declinedStatus = InvitationStatusEnum::DECLINED;

        $rawResult = $this->createQueryBuilder('t')
            ->leftJoin(
                'App\Core\Components\TeaOrCoffeeMeeting\Entity\TeaOrCoffeeMeeting',
                'u',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                't.meeting = u.id'
            )
            ->select('u')
            ->andWhere('t.invitedUser = :user')
            ->andWhere('t.invitationStatus = :status')
            ->setParameter('status', $status)
            ->andWhere('u.date > :actualDate')
            ->setParameter('actualDate', $actualDate)
            ->setParameter('user', $user)
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        $result = [];
        foreach ($rawResult as $item) {
            $user = $this->userRepository->findUserById($item->getInitiator()->getId());
            $result[] = new TeaOrCoffeeMeetingViewModel(
                new TeaOrCoffeeMeetingUserViewModel(
                    $user->getId(),
                    $user->getFirstName(),
                    $user->getLastName(),
                ),
                $this->getInvitedUsersByMeetingId($item->getId()),
                $item->getMeetingType(),
                $item->getDate(),
            );
        }

        return $result;
    }

    private function getInvitedUsersByMeetingId(int $meetingId): ?array
    {
        $rawResult = $this->createQueryBuilder('t')
            ->leftJoin(
                'App\Core\Components\User\Entity\User',
                'u',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                't.invitedUser = u.id'
            )
            ->andWhere('t.meeting = :meetingId')
            ->setParameter('meetingId', $meetingId)
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        $result = [];
        foreach ($rawResult as $item) {
            $result[] = new TeaOrCoffeeMeetingViewModel(
                $item->getInitiator(),
                $item->getInvitedUsersByMeetingId($item->getId()),
                $item->getMeetingType(),
                $item->getDate(),
            );
        }

        return $result;
    }

    // /**
    //  * @return TeaOrCoffeeMeetingUser[] Returns an array of TeaOrCoffeeMeetingUser objects
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

    public function getAllTeaOrCoffeeMeetingsInTenMinutes(): ?array
    {
        $actualDate = new DateTime();
        $dateInTenMinutes = $actualDate->add(new DateInterval('PT' . 10 . 'M'));
        //$dateInTenMinutes = $dateInTenMinutes->format('Y-m-d H:i:00');

        $dateInTenMinutes = "2023-01-27 15:46:00";
        dump($dateInTenMinutes);

        $result = [];

        $rawRequest = $this->createQueryBuilder('t')
            ->leftJoin(
                'App\Core\Components\TeaOrCoffeeMeeting\Entity\TeaOrCoffeeMeeting',
                'u',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                't.meeting = u.id'
            )
            ->select("IDENTITY(u.initiator), IDENTITY(t.invitedUser)")
            ->andWhere("u.date = :dateInTenMinutes")
            ->andWhere('t.invitationStatus = :invitationStatus')
            ->setParameter('invitationStatus', InvitationStatusEnum::ACCEPTED)
            ->setParameter('dateInTenMinutes', $dateInTenMinutes)
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult();

        dd($rawRequest);

        $result = [];
        foreach ($rawRequest as $item) {
            $result[] = $item;
        }

        return $result;
    }
}
