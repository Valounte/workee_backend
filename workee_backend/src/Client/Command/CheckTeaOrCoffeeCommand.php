<?php

namespace App\Client\Command;

use App\Core\Components\TeaOrCoffeeMeeting\Repository\TeaOrCoffeeMeetingRepositoryInterface;
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
class CheckTeaOrCoffee extends Command{
    protected static $defaultDescription = 'Checks if there is a TeaOrCoffee meeting in the next 10 minutes in order to dispatch a notification';

    public function __construct(
        private TeaOrCoffeeMeetingRepositoryInterface $dailyFeedbackTeamPreferencesRepository,
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
        return Command::SUCCESS;
    }
}