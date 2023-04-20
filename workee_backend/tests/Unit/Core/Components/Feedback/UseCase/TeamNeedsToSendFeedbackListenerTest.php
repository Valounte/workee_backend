<?php

namespace App\Tests\Unit\Core\Components\Feedback\UseCase;

use DateTime;
use App\Tests\Unit\StubTeamFactory;
use App\Tests\Unit\StubUserFactory;
use App\Tests\Unit\AbstractTestCase;
use Symfony\Component\Mercure\Update;
use App\Core\Components\Team\Entity\Team;
use Symfony\Component\Mercure\HubInterface;
use App\Core\Components\Company\Entity\Company;
use App\Infrastructure\Token\Services\TokenService;
use App\Infrastructure\Team\Repository\TeamRepository;
use App\Infrastructure\User\Repository\UserTeamRepository;
use App\Core\Components\Feedback\UseCase\TeamNeedsToSendFeedbackEvent;
use App\Core\Components\Feedback\UseCase\TeamNeedsToSendFeedbackListener;

final class TeamNeedsToSendFeedbackListenerTest extends AbstractTestCase
{
    private TeamNeedsToSendFeedbackListener $listener;

    private UserTeamRepository $userTeamRepository;

    private HubInterface $hub;

    private TeamRepository $teamRepository;

    private TokenService $tokenService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userTeamRepository = $this->createMock(UserTeamRepository::class);
        $this->hub = $this->createMock(HubInterface::class);
        $this->tokenService = new TokenService();
        $this->teamRepository = $this->createMock(TeamRepository::class);
        $this->listener = new TeamNeedsToSendFeedbackListener(
            $this->tokenService,
            $this->userTeamRepository,
            $this->hub,
            $this->teamRepository,
            'mercure_hub_url',
        );
    }

    public function test_team_needs_to_send_feedback_listener(): void
    {
        $event = new TeamNeedsToSendFeedbackEvent(StubTeamFactory::create(1), '11:30');

        $this->userTeamRepository
            ->expects($this->once())
            ->method('findUsersByTeamId')
            ->willReturn([StubUserFactory::create(1)]);

        $this->hub
            ->expects($this->once())
            ->method('publish')
            ->with(new Update(
                'mercure_hub_url/feedback/' . $this->tokenService->createLoginToken(StubUserFactory::create(1)),
                json_encode([
                    'message' => "needs to send feedback",
                    'teamId' => 1,
                    'type' => 'feedback',
                ])
            ));

        $this->listener->__invoke($event);
    }
}
