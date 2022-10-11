<?php

namespace App\Core\Components\Feedback\UseCase;

use DateTime;
use DateInterval;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use App\Infrastructure\Token\Services\TokenService;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Core\Components\User\Repository\UserTeamRepositoryInterface;
use App\Core\Components\Feedback\UseCase\SelectDailyFeedbackTeamPreferencesCommand;
use App\Core\Components\Feedback\Repository\DailyFeedbackTeamPreferencesRepositoryInterface;

final class SelectDailyFeedbackTeamPreferencesHandler implements MessageHandlerInterface
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private DailyFeedbackTeamPreferencesRepositoryInterface $dailyFeedbackTeamPreferencesRepository,
        private string $mercureHubUrl,
        private TokenService $tokenService,
        private HubInterface $hub,
        private UserTeamRepositoryInterface $userTeamRepository,
    ) {
    }

    public function __invoke(SelectDailyFeedbackTeamPreferencesCommand $command): void
    {
        $sendingTime = $command->getSendingTime();

        $dailyFeedbackTeamPreferences = $this->dailyFeedbackTeamPreferencesRepository->findByTeam($command->getTeam());

        if ($dailyFeedbackTeamPreferences->getSendingTime() !== $sendingTime->format('H:i')) {
            return;
        }

        $users = $this->userTeamRepository->findUsersByTeamId($command->getTeam()->getId());

        foreach ($users as $user) {
            $jwt = $this->tokenService->createLoginToken($user);

            $update = new Update(
                $this->mercureHubUrl . '/daily-feedback' . '/' . $jwt,
                json_encode([
                    'message' => "daily feedback time",
                    'teamName' => $command->getTeam()->getTeamName(),
                ])
            );
            $this->hub->publish($update);
        }

        $sendingTime->add(new DateInterval('P1D'));
        $this->messageBus->dispatch(
            new Envelope(
                new SelectDailyFeedbackTeamPreferencesCommand($sendingTime, $command->getTeam()),
                [$this->getDelayStampFromNowToDatetime($sendingTime)],
            ),
        );
    }

    private function getDelayStampFromNowToDatetime(DateTime $datetime): DelayStamp
    {
        $now = new DateTime();
        $intervalInSeconds = $datetime->getTimestamp() - $now->getTimestamp();

        return new DelayStamp($intervalInSeconds * 1000);
    }
}
