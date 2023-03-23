<?php

namespace App\Client\Command;

use App\Core\Components\Feedback\Entity\DailyFeedbackTeamPreferences;
use App\Core\Components\Feedback\Repository\DailyFeedbackTeamPreferencesRepositoryInterface;
use App\Core\Components\Feedback\UseCase\TeamNeedsToSendFeedbackEvent;
use App\Core\Components\Feedback\UseCase\TeamNeedsToSendFeedbackListener;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:checkDailyFeedbackPreferences',
    description: 'Checks if there is Daily Feedbacks to send in the next minute in order to put them in queue',
    hidden: false,
)]
class CheckDailyFeedbackTeamPreferencesCommand extends Command
{
    protected static $defaultDescription = 'Checks if there is Daily Feedbacks to send in the next minute in order to put them in queue';

    public function __construct(
        private DailyFeedbackTeamPreferencesRepositoryInterface $dailyFeedbackTeamPreferencesRepository,
        private MessageBusInterface $messageBus,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            // the command help shown when running the command with the "--help" option
            ->setHelp('This command allows you to Checks if there is Daily feedbacks to send in the next minute in order to send an asynchronous mercure notification...');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dailyFeedbacksPreferences = $this->dailyFeedbackTeamPreferencesRepository->findPreferencesInNextMinute();

        if ($dailyFeedbacksPreferences === []) {
            return Command::SUCCESS;
        }

        foreach ($dailyFeedbacksPreferences as $dailyFeedbackPreference) {
            $event = new TeamNeedsToSendFeedbackEvent($dailyFeedbackPreference->getTeam(), $dailyFeedbackPreference->getSendingTime());
            $this->messageBus->dispatch($event);
        }
        return Command::SUCCESS;
    }
}
