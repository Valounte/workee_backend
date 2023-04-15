<?php

namespace App\Core\Components\TeaOrCoffeeMeeting\UseCase;

use App\Core\Components\User\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Core\Components\TeaOrCoffeeMeeting\Entity\TeaOrCoffeeMeeting;
use App\Client\ViewModel\TeaOrCoffeeMeeting\TeaOrCoffeeMeetingViewModel;
use App\Core\Components\TeaOrCoffeeMeeting\Entity\TeaOrCoffeeMeetingUser;
use App\Client\ViewModel\TeaOrCoffeeMeeting\TeaOrCoffeeMeetingUserViewModel;
use App\Core\Components\TeaOrCoffeeMeeting\Entity\Enum\TeaOrCoffeeMeetingTypeEnum;
use App\Client\ViewModel\TeaOrCoffeeMeeting\TeaOrCoffeeMeetingInvitedUserViewModel;
use App\Core\Components\TeaOrCoffeeMeeting\UseCase\CreateTeaOrCoffeeMeetingCommand;
use App\Core\Components\TeaOrCoffeeMeeting\Repository\TeaOrCoffeeMeetingRepositoryInterface;
use App\Core\Components\TeaOrCoffeeMeeting\Repository\TeaOrCoffeeMeetingUserRepositoryInterface;

final class CreateTeaOrCoffeeMeetingHandler implements MessageHandlerInterface
{
    public function __construct(
        private TeaOrCoffeeMeetingRepositoryInterface $teaOrCoffeeMeetingRepository,
        private UserRepositoryInterface $userRepository,
        private TeaOrCoffeeMeetingUserRepositoryInterface $teaOrCoffeeMeetingUserRepository,
    ) {
    }

    public function __invoke(CreateTeaOrCoffeeMeetingCommand $command): TeaOrCoffeeMeetingViewModel
    {
        $invitedUsersIds = $command->getInvitedUsersIds();

        $invitedUsers = [];
        foreach ($invitedUsersIds as $invitedUserId) {
            $invitedUsers[] = $this->userRepository->findUserById($invitedUserId);
        }

        $teaOrCoffeeMeeting = new TeaOrCoffeeMeeting(
            $command->getInitiator(),
            $command->getDate(),
            TeaOrCoffeeMeetingTypeEnum::CLASSIC,
            $command->getName(),
        );
        $this->teaOrCoffeeMeetingRepository->add($teaOrCoffeeMeeting);

        $invitedUsersViewModels = [];
        foreach ($invitedUsers as $invitedUser) {
            $teaOrCoffeeMeetingUser = new TeaOrCoffeeMeetingUser($teaOrCoffeeMeeting, $invitedUser);
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
