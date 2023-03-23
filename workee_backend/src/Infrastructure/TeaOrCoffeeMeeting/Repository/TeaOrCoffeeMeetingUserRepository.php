<?php

namespace App\Infrastructure\TeaOrCoffeeMeeting\Repository;

use DateTime;
use DateInterval;
use DateTimeZone;
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
            ->getResult();

        $result = [];
        foreach ($rawResult as $item) {
            $user = $this->userRepository->findUserById($item->getInitiator()->getId());
            $result[] = new TeaOrCoffeeMeetingViewModel(
                $item->getId(),
                new TeaOrCoffeeMeetingUserViewModel(
                    $user->getId(),
                    $user->getFirstName(),
                    $user->getLastName(),
                ),
                $this->getInvitedUsersByMeetingId($item->getId()),
                $item->getMeetingType(),
                $item->getDate(),
                $item->getName(),
            );
        }

        return $result;
    }

    public function getAllTeaOrCoffeeMeetingByUser(User $user): ?array
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
            ->andWhere('t.invitedUser = :user OR u.initiator = :user')
            ->andWhere('u.date > :actualDate')
            ->setParameter('actualDate', $actualDate)
            ->setParameter('user', $user)
            ->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult();

        $result = [];
        foreach ($rawResult as $item) {
            $user = $this->userRepository->findUserById($item->getInitiator()->getId());
            $result[] = new TeaOrCoffeeMeetingViewModel(
                $item->getId(),
                new TeaOrCoffeeMeetingUserViewModel(
                    $user->getId(),
                    $user->getFirstName(),
                    $user->getLastName(),
                ),
                $this->getInvitedUsersByMeetingId($item->getId()),
                $item->getMeetingType(),
                $item->getDate(),
                $item->getName(),
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
            ->getResult();

        $result = [];
        foreach ($rawResult as $item) {
            $result[] = new TeaOrCoffeeMeetingInvitedUserViewModel(
                new TeaOrCoffeeMeetingUserViewModel(
                    $item->getInvitedUser()->getId(),
                    $item->getInvitedUser()->getFirstname(),
                    $item->getInvitedUser()->getLastname(),
                ),
                $item->getInvitationStatus(),
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

    public function getAllTeaOrCoffeeMeetingUserIdsInTenMinutes(): ?array
    {
        $actualDate = new DateTime(timezone: new DateTimeZone('Europe/Paris'));
        $dateInTenMinutes = $actualDate->add(new DateInterval('PT' . 10 . 'M'));
        $dateInTenMinutes = $dateInTenMinutes->format('Y-m-d H:i:s');
        $dateInTenMinutes = substr($dateInTenMinutes, 0, -2) . '00';

        $rawRequest = $this->createQueryBuilder('t')
            ->leftJoin(
                'App\Core\Components\TeaOrCoffeeMeeting\Entity\TeaOrCoffeeMeeting',
                'u',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                't.meeting = u.id'
            )
            ->select("IDENTITY(u.initiator) as initiator, IDENTITY(t.invitedUser) as invitedUser, u.name as name")
            ->andWhere("u.date = :dateInTenMinutes")
            ->andWhere('t.invitationStatus = :invitationStatus')
            ->setParameter('invitationStatus', InvitationStatusEnum::ACCEPTED)
            ->setParameter('dateInTenMinutes', $dateInTenMinutes)
            ->getQuery()
            ->getResult();

        $result = [];
        $result = array_reduce($rawRequest, function ($acc, $item) {
            $key = $item['initiator'] . ':' . $item['name'];
            if (!isset($acc[$key])) {
                $acc[$key] = [
                    'initiator' => $item['initiator'],
                    'name' => $item['name'],
                    'invitedUsers' => []
                ];
            }
            $acc[$key]['invitedUsers'][] = $item['invitedUser'];
            return $acc;
        }, []);

        $result = array_values($result);
        return $result;
    }
}
