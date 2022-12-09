<?php

namespace App\Core\Components\Feedback\UseCase;
use App\Infrastructure\Token\Services\TokenService;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Core\Components\Feedback\UseCase\TeamNeedsToSendFeedbackEvent;
use App\Core\Components\Team\Repository\TeamRepositoryInterface;
use App\Core\Components\User\Repository\UserTeamRepositoryInterface;

final class TeamNeedsToSendFeedbackListener implements MessageHandlerInterface {
    public function __construct(
        private TokenService $tokenService,
        private UserTeamRepositoryInterface $userTeamRepository,
        private HubInterface $hub,
        private TeamRepositoryInterface $teamRepository,
        private string $mercureHubUrl,
    ) {
    }

    public function __invoke(TeamNeedsToSendFeedbackEvent $teamNeedsToSendFeedback)
    {
        $team = $teamNeedsToSendFeedback->getTeam();
        $users = $this->userTeamRepository->findUsersByTeamId($team);

        foreach ($users as $user) {
            $jwt = $this->tokenService->createLoginToken($user);
            $update = new Update(
                $this->mercureHubUrl . '/feedback' . '/' . $jwt,
            );

            $this->hub->publish($update);
        }
    }
}
