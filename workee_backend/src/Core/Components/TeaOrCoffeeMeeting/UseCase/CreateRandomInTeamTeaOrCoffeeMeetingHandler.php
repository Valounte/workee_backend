<?php

namespace App\Core\Components\TeaOrCoffeeMeeting\UseCase;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Core\Components\User\Repository\UserTeamRepositoryInterface;
use App\Core\Components\TeaOrCoffeeMeeting\Entity\TeaOrCoffeeMeeting;
use App\Core\Components\TeaOrCoffeeMeeting\Entity\Enum\TeaOrCoffeeMeetingTypeEnum;
use App\Core\Components\TeaOrCoffeeMeeting\Entity\TeaOrCoffeeMeetingUser;
use App\Core\Components\TeaOrCoffeeMeeting\Repository\TeaOrCoffeeMeetingRepositoryInterface;
use App\Core\Components\TeaOrCoffeeMeeting\Repository\TeaOrCoffeeMeetingUserRepositoryInterface;
use App\Core\Components\TeaOrCoffeeMeeting\UseCase\CreateRandomInTeamTeaOrCoffeeMeetingCommand;

final class CreateRandomInTeamTeaOrCoffeeMeetingHandler implements MessageHandlerInterface
{
    public function __construct(
        private TeaOrCoffeeMeetingRepositoryInterface $teaOrCoffeeMeetingRepository,
        private UserTeamRepositoryInterface $userTeamRepository,
        private TeaOrCoffeeMeetingUserRepositoryInterface $teaOrCoffeeMeetingUserRepository,
    ) {
    }

    public function __invoke(CreateRandomInTeamTeaOrCoffeeMeetingCommand $command): void
    {
        $usersInTeam = $this->userTeamRepository->findUsersByTeamId($command->getTeam()->getId());
        $initiator = $command->getInitiator();

        $randomUserInTeam = $usersInTeam[array_rand($usersInTeam)];
        while ($randomUserInTeam->getId() === $initiator->getId()) {
            $randomUserInTeam = $usersInTeam[array_rand($usersInTeam)];
        }

        $teaOrCoffeeMeeting = new TeaOrCoffeeMeeting(
            $command->getInitiator(),
            $command->getDate(),
            TeaOrCoffeeMeetingTypeEnum::RANDOM_IN_TEAM,
            $command->getName(),
        );
        $this->teaOrCoffeeMeetingRepository->add($teaOrCoffeeMeeting);

        $teaOrCoffeeMeetingUser = new TeaOrCoffeeMeetingUser($teaOrCoffeeMeeting, $randomUserInTeam);
        $this->teaOrCoffeeMeetingUserRepository->add($teaOrCoffeeMeetingUser);
    }
}
