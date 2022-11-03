<?php

namespace App\Client\Controller\Feedback;

use DateTime;
use DateInterval;
use App\Core\Components\Team\Entity\Team;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBus;
use App\Client\ViewModel\Team\TeamViewModel;
use App\Client\ViewModel\User\UserViewModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use App\Client\ViewModel\Company\CompanyViewModel;
use App\Client\ViewModel\Feedback\DailyFeedbackPreferencesViewModel;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Core\Components\User\Service\GetUserService;
use Symfony\Component\Messenger\MessageBusInterface;
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
use App\Core\Components\Feedback\Entity\DailyFeedbackTeamPreferences;
use App\Core\Components\Feedback\Repository\DailyFeedbackRepositoryInterface;
use App\Core\Components\Feedback\UseCase\SelectDailyFeedbackTeamPreferencesCommand;
use App\Core\Components\Feedback\Repository\DailyFeedbackTeamPreferencesRepositoryInterface;
use App\Core\Components\Feedback\UseCase\SelectDailyFeedbackTeamPreferencesHandler;

final class DailyFeedbackController extends AbstractController
{
    public function __construct(
        private CheckUserPermissionsService $checkUserPermissionsService,
        private UserTeamRepositoryInterface $userTeamRepository,
        private JsonResponseService $jsonResponseService,
        private DailyFeedbackRepositoryInterface $dailyFeedbackRepository,
        private TeamRepositoryInterface $teamRepository,
        private GetUserService $getUserService,
        private DailyFeedbackTeamPreferencesRepositoryInterface $dailyFeedbackTeamPreferencesRepository,
        private MessageBusInterface $messageBus,
        private SelectDailyFeedbackTeamPreferencesHandler $selectDailyFeedbackTeamPreferencesHandler,
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

        $message = null;
        if (isset($input["message"])) {
            $message = $input["message"];
        }

        foreach ($userTeams as $userTeam) {
            $dailyFeedback = new DailyFeedback(
                $input["satisfactionDegree"],
                $userTeam,
                $message,
                $isAnonymous == true ? null : $user,
            );

            $this->dailyFeedbackRepository->add($dailyFeedback, true);
        }

        return $this->jsonResponseService->successJsonResponse('Feedback stored', 200);
    }

    /**
     * @Route("/api/last-week-daily-feedback", name="getLastWeekDailyFeedback", methods={"GET"})
     */
    public function getLastWeekDailyFeedback(Request $request): Response
    {
        try {
            $user = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException|UserNotFoundException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $team = $this->teamRepository->findOneById($request->query->get('teamId'));


        $dailyFeedbackViewModel = $this->dailyFeedbackRepository->findLastWeekDailyFeedbackByTeam($team);

        $allSatisfactionDegree = [];
        foreach ($dailyFeedbackViewModel as $feedback) {
            $allSatisfactionDegree[] = $feedback->getSatisfactionDegree();
        }


        $lastWeekDailyFeedbackViewModel = new LastWeekDailyFeedbackViewModel(
            array_sum($allSatisfactionDegree) / count($allSatisfactionDegree),
            $dailyFeedbackViewModel,
            new TeamViewModel($team->getId(), $team->getTeamName(), $team->getDescription()),
        );

        return $this->jsonResponseService->create($lastWeekDailyFeedbackViewModel, 200);
    }

    /**
     * @Route("/api/daily-feedback-preferences", name="getDailyFeedbackPreferences", methods={"GET"})
     */
    public function getDailyFeedbackPreferences(Request $request): Response
    {
        try {
            $user = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException|UserNotFoundException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $teams = $this->userTeamRepository->findTeamsByUser($user);


        $dailyFeedbackPreferencesViewModels = [];
        foreach ($teams as $team) {
            $dailyFeedbackTeamPreferences = $this->dailyFeedbackTeamPreferencesRepository->findByTeam($team);

            if ($dailyFeedbackTeamPreferences !== null) {
                $dailyFeedbackPreferencesViewModels[] = new DailyFeedbackPreferencesViewModel(
                    true,
                    $dailyFeedbackTeamPreferences->getSendingTime(),
                    $this->createDailyFeedbackSendingCronjobTime($dailyFeedbackTeamPreferences->getSendingTime()),
                    $team->getId(),
                );
            } else {
                $dailyFeedbackPreferencesViewModels[] = new DailyFeedbackPreferencesViewModel(
                    false,
                    null,
                    null,
                    $team->getId(),
                );
            }
        }

        return $this->jsonResponseService->create($dailyFeedbackPreferencesViewModels, 200);
    }

    /**
     * @Route("/api/register-daily-feedback-preferences", name="registerDailyFeedbackPreferences", methods={"POST"})
     */
    public function registerDailyFeedbackPreferences(Request $request): Response
    {
        try {
            $user = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException|UserNotFoundException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $input = json_decode($request->getContent(), true);
        $team = $this->teamRepository->findOneById($input["teamId"]);
        $dailyFeedbackTeamPreferences = $this->dailyFeedbackTeamPreferencesRepository->findByTeam($team);

        if ($dailyFeedbackTeamPreferences == null) {
            $dailyFeedbackTeamPreferences = $this->createNewDailyFeedbackTeamPreferences($input["sendingTime"], $team);
            return $this->jsonResponseService->successJsonResponse('Daily feedback preferences registered', 200);
        }

        $dailyFeedbackTeamPreferences->setSendingTime($input["sendingTime"]);
        $this->dailyFeedbackTeamPreferencesRepository->add($dailyFeedbackTeamPreferences, true);

        return $this->jsonResponseService->successJsonResponse('Feedback preferences modified', 200);
    }

    /**
     * @Route("/api/teams-daily-feedback", name="teamDailyFeedback", methods={"GET"})
     */
    public function teamsDailyFeedback(Request $request): Response
    {
        try {
            $user = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException|UserNotFoundException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $teams = $this->userTeamRepository->findTeamsByUser($user);

        $lastWeekDailyFeedbackViewModel = [];
        foreach ($teams as $team) {
            $teamFeedbackViewModel = $this->dailyFeedbackRepository->findLastWeekDailyFeedbackByTeam($team);
            $lastWeekDailyFeedbackViewModel[] = new LastWeekDailyFeedbackViewModel(
                $this->getAverageSatisfactionDegreeOfATeam($teamFeedbackViewModel),
                $teamFeedbackViewModel,
                new TeamViewModel($team->getId(), $team->getTeamName(), $team->getDescription()),
            );
        }

        return $this->jsonResponseService->create($lastWeekDailyFeedbackViewModel, 200);
    }

    private function createDailyFeedbackSendingCronjobTime(string $sendingTime): string
    {
        $sendingTime = explode(':', $sendingTime);
        $hour = $sendingTime[0];
        $minute = $sendingTime[1];

        return $minute . ' ' . $hour . ' * * *';
    }

    private function createNewDailyFeedbackTeamPreferences(string $sendingTime, Team $team): DailyFeedbackTeamPreferences
    {
        $dailyFeedbackTeamPreferences = new DailyFeedbackTeamPreferences(
            $sendingTime,
            $team,
        );

        $this->dailyFeedbackTeamPreferencesRepository->add($dailyFeedbackTeamPreferences, true);

        return $dailyFeedbackTeamPreferences;
    }


    private function getAverageSatisfactionDegreeOfATeam(array $dailyFeedbackViewModel): float
    {
        $allSatisfactionDegree = [];
        foreach ($dailyFeedbackViewModel as $feedback) {
            $allSatisfactionDegree[] = $feedback->getSatisfactionDegree();
        }

        return array_sum($allSatisfactionDegree) / count($allSatisfactionDegree);
    }
}
