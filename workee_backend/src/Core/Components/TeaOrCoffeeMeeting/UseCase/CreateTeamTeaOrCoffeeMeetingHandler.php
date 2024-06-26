<?php

namespace App\Core\Components\TeaOrCoffeeMeeting\UseCase;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Core\Components\User\Repository\UserTeamRepositoryInterface;
use App\Core\Components\TeaOrCoffeeMeeting\Entity\TeaOrCoffeeMeeting;
use App\Client\ViewModel\TeaOrCoffeeMeeting\TeaOrCoffeeMeetingViewModel;
use App\Core\Components\TeaOrCoffeeMeeting\Entity\TeaOrCoffeeMeetingUser;
use App\Client\ViewModel\TeaOrCoffeeMeeting\TeaOrCoffeeMeetingUserViewModel;
use App\Core\Components\TeaOrCoffeeMeeting\Entity\Enum\TeaOrCoffeeMeetingTypeEnum;
use App\Client\ViewModel\TeaOrCoffeeMeeting\TeaOrCoffeeMeetingInvitedUserViewModel;
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

    public function __invoke(CreateTeamTeaOrCoffeeMeetingCommand $command): TeaOrCoffeeMeetingViewModel
    {
        $usersInTeam = $this->userTeamRepository->findUsersByTeamId($command->getTeam()->getId());

        $teaOrCoffeeMeeting = new TeaOrCoffeeMeeting(
            $command->getInitiator(),
            $command->getDate(),
            TeaOrCoffeeMeetingTypeEnum::TEAM,
            $command->getName(),
        );
        $this->teaOrCoffeeMeetingRepository->add($teaOrCoffeeMeeting);

        $invitedUsersViewModels = [];
        foreach ($usersInTeam as $userInTeam) {
            $teaOrCoffeeMeetingUser = new TeaOrCoffeeMeetingUser($teaOrCoffeeMeeting, $userInTeam);
            $this->teaOrCoffeeMeetingUserRepository->add($teaOrCoffeeMeetingUser);

            $invitedUsersViewModels[] = new TeaOrCoffeeMeetingInvitedUserViewModel(
                new TeaOrCoffeeMeetingUserViewModel(
                    $teaOrCoffeeMeetingUser->getInvitedUser()->getId(),
                    $teaOrCoffeeMeetingUser->getInvitedUser()->getFirstName(),
                    $teaOrCoffeeMeetingUser->getInvitedUser()->getLastName(),
                ),
                $teaOrCoffeeMeetingUser->getInvitationStatus(),
            );
        }

        return new TeaOrCoffeeMeetingViewModel(
            $teaOrCoffeeMeeting->getId(),
            new TeaOrCoffeeMeetingUserViewModel(
                $teaOrCoffeeMeeting->getInitiator()->getId(),
                $teaOrCoffeeMeeting->getInitiator()->getFirstName(),
                $teaOrCoffeeMeeting->getInitiator()->getLastName(),
            ),
            $invitedUsersViewModels,
            $teaOrCoffeeMeeting->getMeetingType(),
            $teaOrCoffeeMeeting->getDate(),
            $teaOrCoffeeMeeting->getName(),
        );
    }
}
