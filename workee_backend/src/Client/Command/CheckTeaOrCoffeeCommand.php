<?php

namespace App\Client\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Core\Components\User\Repository\UserRepositoryInterface;
use App\Core\Components\TeaOrCoffeeMeeting\UseCase\UserHasMeetingInTenMinutesEvent;
use App\Core\Components\TeaOrCoffeeMeeting\Repository\TeaOrCoffeeMeetingUserRepositoryInterface;

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
            ->setHelp('This command allows you to Checks if there is a TeaOrCoffee meeting in the next 10 minute in order to send an asynchronous mercure notification...');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $teaOrCoffeeToNotify = $this->teaOrCoffeeUserRepository->getAllTeaOrCoffeeMeetingUserIdsInTenMinutes();

        if ($teaOrCoffeeToNotify === []) {
            return Command::SUCCESS;
        }

        foreach ($teaOrCoffeeToNotify as $teaOrCoffee) {
            $event = new UserHasMeetingInTenMinutesEvent(
                (int) $teaOrCoffee['initiator'],
                $teaOrCoffee['invitedUsers'],
                $teaOrCoffee['name']
            );
            $this->messageBus->dispatch($event);
        }

        return Command::SUCCESS;
    }
}
