<?php

namespace App\Client\Command;

use App\Core\Components\Feedback\UseCase\UserHasMeetingInTenMinutesEvent as UseCaseUserHasMeetingInTenMinutesEvent;
use App\Core\Components\TeaOrCoffeeMeeting\Repository\TeaOrCoffeeMeetingUserRepositoryInterface;
use App\Core\Components\User\Repository\UserRepositoryInterface;
use App\Core\Components\TeaOrCoffeeMeeting\UseCase\UserHasMeetingInTenMinutesEvent;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:checkTeaOrCoffee',
    description: 'Checks if there is a TeaOrCoffee meeting in the next 10 minutes in order to dispatch a notification',
    hidden: false,
)]
class CheckTeaOrCoffeeCommand extends Command
{
    protected static $defaultDescription = 'Checks if there is a TeaOrCoffee meeting in the next 10 minutes in order to dispatch a notification';

    public function __construct(
        private TeaOrCoffeeMeetingUserRepositoryInterface $teaOrCoffeeUserRepository,
        private UserRepositoryInterface $userInterface,
        private MessageBusInterface $messageBus,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            // the command help shown when running the command with the "--help" option
            ->setHelp('This command allows you to Checks if there is a TeaOrCoffee meeting in the next 10 minute in order to send an asynchronous mercure notification...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $usersToNotify = $this->teaOrCoffeeUserRepository->getAllTeaOrCoffeeMeetingsInTenMinutes();

        if ($usersToNotify === []) {
            return Command::SUCCESS;
        }

        foreach ($usersToNotify as $user) {
            $event = new UseCaseUserHasMeetingInTenMinutesEvent($user->getUserId());
            $this->messageBus->dispatch($event);
        }
        
        return Command::SUCCESS;
    }
}