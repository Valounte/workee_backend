<?php

namespace App\Core\Components\TeaOrCoffeeMeeting\UseCase;

use App\Core\Components\User\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Core\Components\TeaOrCoffeeMeeting\Entity\TeaOrCoffeeMeeting;
use App\Core\Components\TeaOrCoffeeMeeting\Entity\TeaOrCoffeeMeetingUser;
use App\Core\Components\TeaOrCoffeeMeeting\Entity\Enum\TeaOrCoffeeMeetingTypeEnum;
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

    public function __invoke(CreateTeaOrCoffeeMeetingCommand $command): void
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
        );
        $this->teaOrCoffeeMeetingRepository->add($teaOrCoffeeMeeting);

        foreach ($invitedUsers as $invitedUser) {
            $teaOrCoffeeMeetingUser = new TeaOrCoffeeMeetingUser($teaOrCoffeeMeeting, $invitedUser);
            $this->teaOrCoffeeMeetingUserRepository->add($teaOrCoffeeMeetingUser);
        }
    }
}
