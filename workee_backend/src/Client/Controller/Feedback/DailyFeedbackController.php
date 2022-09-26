<?php

namespace App\Client\Controller\Feedback;

use App\Client\ViewModel\Team\TeamViewModel;
use App\Client\ViewModel\User\UserViewModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Client\ViewModel\Company\CompanyViewModel;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Core\Components\User\Service\GetUserService;
use App\Core\Components\Feedback\Entity\DailyFeedback;
use App\Client\ViewModel\Feedback\DailyFeedbackViewModel;
use App\Infrastructure\Response\Services\JsonResponseService;
use App\Core\Components\Team\Repository\TeamRepositoryInterface;
use App\Infrastructure\User\Exceptions\UserPermissionsException;
use App\Client\ViewModel\Feedback\LastWeekDailyFeedbackViewModel;
use App\Infrastructure\User\Services\CheckUserPermissionsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Core\Components\User\Repository\UserTeamRepositoryInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use App\Core\Components\Feedback\Repository\DailyFeedbackRepositoryInterface;

final class DailyFeedbackController extends AbstractController
{
    public function __construct(
        private CheckUserPermissionsService $checkUserPermissionsService,
        private UserTeamRepositoryInterface $userTeamRepository,
        private JsonResponseService $jsonResponseService,
        private DailyFeedbackRepositoryInterface $dailyFeedbackRepository,
        private TeamRepositoryInterface $teamRepository,
        private GetUserService $getUserService,
    ) {
    }

    /**
     * @Route("/api/submit-daily-feedback", name="submitDailyFeedback", methods={"POST"})
     */
    public function submitDailyFeedback(Request $request): Response
    {
        try {
            $user = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException|UserNotFoundException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $input = json_decode($request->getContent(), true);

        $userTeams = $this->userTeamRepository->findTeamsByUser($user);

        $isAnonymous = $input["isAnonymous"] ?? false;

        foreach ($userTeams as $userTeam) {
            $dailyFeedback = new DailyFeedback(
                $input["satisfactionDegree"],
                $userTeam,
                $isAnonymous == true ? null : $user,
            );

            $this->dailyFeedbackRepository->add($dailyFeedback, true);
        }

        return $this->jsonResponseService->successJsonResponse('Feedback stored', 200);
    }

    /**
     * @Route("/api/last-week-daily-feedback/{teamId}", name="getLastWeekDailyFeedback", methods={"GET"})
     */
    public function getLastWeekDailyFeedback(int $teamId, Request $request): Response
    {
        try {
            $user = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException|UserNotFoundException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $team = $this->teamRepository->findOneById($teamId);

        $allFeedback = $this->dailyFeedbackRepository->findLastWeekDailyFeedbackByTeam($team);

        $dailyFeedbackViewModel = [];
        $allSatisfactionDegree = [];

        foreach ($allFeedback as $feedback) {
            $userViewModel = null;
            if ($feedback->getUser() != null) {
                $userViewModel = $this->getUserService->createUserViewModel($feedback->getUser());
            }

            $allSatisfactionDegree[] = $feedback->getSatisfactionDegree();

            $dailyFeedbackViewModel[] = new DailyFeedbackViewModel(
                $feedback->getId(),
                $feedback->getSatisfactionDegree(),
                $userViewModel,
            );
        }


        $lastWeekDailyFeedbackViewModel = new LastWeekDailyFeedbackViewModel(
            random_int(1, 50000),
            array_sum($allSatisfactionDegree) / count($allSatisfactionDegree),
            $dailyFeedbackViewModel,
            new TeamViewModel($team->getId(), $team->getTeamName()),
        );

        return $this->jsonResponseService->create($lastWeekDailyFeedbackViewModel, 200);
    }
}
