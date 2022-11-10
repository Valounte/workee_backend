<?php

namespace App\Core\Components\TeaOrCoffeeMeeting\UseCase;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Core\Components\User\Repository\UserTeamRepositoryInterface;
use App\Core\Components\TeaOrCoffeeMeeting\Entity\TeaOrCoffeeMeeting;
use App\Core\Components\TeaOrCoffeeMeeting\Entity\TeaOrCoffeeMeetingUser;
use App\Core\Components\TeaOrCoffeeMeeting\Entity\Enum\TeaOrCoffeeMeetingTypeEnum;
use App\Core\Components\TeaOrCoffeeMeeting\UseCase\CreateTeamTeaOrCoffeeMeetingCommand;
use App\Core\Components\TeaOrCoffeeMeeting\Repository\TeaOrCoffeeMeetingRepositoryInterface;
use App\Core\Components\TeaOrCoffeeMeeting\Repository\TeaOrCoffeeMeetingUserRepositoryInterface;

final class CreateTeamTeaOrCoffeeMeetingHandler implements MessageHandlerInterface
{
    public function __construct(
        private TeaOrCoffeeMeetingRepositoryInterface $teaOrCoffeeMeetingRepository,
        private UserTeamRepositoryInterface $userTeamRepository,
        private TeaOrCoffeeMeetingUserRepositoryInterface $teaOrCoffeeMeetingUserRepository,
    ) {
    }

    public function __invoke(CreateTeamTeaOrCoffeeMeetingCommand $command): void
    {
        $usersInTeam = $this->userTeamRepository->findUsersByTeamId($command->getTeam()->getId());

        $teaOrCoffeeMeeting = new TeaOrCoffeeMeeting(
            $command->getInitiator(),
            $command->getDate(),
            TeaOrCoffeeMeetingTypeEnum::TEAM,
        );
        $this->teaOrCoffeeMeetingRepository->add($teaOrCoffeeMeeting);

        foreach ($usersInTeam as $userInTeam) {
            $teaOrCoffeeMeetingUser = new TeaOrCoffeeMeetingUser($teaOrCoffeeMeeting, $userInTeam);
            $this->teaOrCoffeeMeetingUserRepository->add($teaOrCoffeeMeetingUser);
        }
    }
}
