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
use App\Core\Components\Feedback\UseCase\testService;

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
            array_sum($allSatisfactionDegree) / count($allSatisfactionDegree),
            $dailyFeedbackViewModel,
            new TeamViewModel($team->getId(), $team->getTeamName()),
        );

        return $this->jsonResponseService->create($lastWeekDailyFeedbackViewModel, 200);
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
        } else {
            $dailyFeedbackTeamPreferences->setSendingTime($input["sendingTime"]);
            $this->dailyFeedbackTeamPreferencesRepository->add($dailyFeedbackTeamPreferences, true);
        }


        $formattedSendingTime = explode(':', $input['sendingTime']);
        $sendingTime = new DateTime();
        $sendingTime->setTime($formattedSendingTime[0], $formattedSendingTime[1]);

        if ($sendingTime < new DateTime()) {
            $sendingTime->add(new DateInterval('P1D'));
        }

        $this->messageBus->dispatch(
            new Envelope(
                new SelectDailyFeedbackTeamPreferencesCommand($sendingTime, $team),
                [$this->getDelayStampFromNowToDatetime($sendingTime)]
            ),
        );

        // $this->selectDailyFeedbackTeamPreferencesHandler->__invoke(
        //     new SelectDailyFeedbackTeamPreferencesCommand($sendingTime, $team),
        // );

        return $this->jsonResponseService->successJsonResponse('Daily feedback preferences registered', 200);
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

    private function getDelayStampFromNowToDatetime(DateTime $datetime): DelayStamp
    {
        $now = new DateTime();
        $intervalInSeconds = $datetime->getTimestamp() - $now->getTimestamp();

        return new DelayStamp($intervalInSeconds * 1000);
    }
}
