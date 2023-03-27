<?php

namespace App\Client\Controller\ProfessionalDevelopment;

use App\Client\ViewModel\ProfessionalDevelopment\GoalViewModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Core\Components\Logs\Entity\Enum\LogsAlertEnum;
use App\Core\Components\Logs\Entity\Enum\LogsContextEnum;
use App\Core\Components\Logs\Services\LogsServiceInterface;
use App\Core\Components\ProfessionalDevelopment\Entity\Enum\GoalStatusEnum;
use App\Infrastructure\Response\Services\JsonResponseService;
use App\Core\Components\User\Repository\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Core\Components\User\Repository\UserTeamRepositoryInterface;
use App\Core\Components\ProfessionalDevelopment\Entity\ProfessionalDevelopmentGoal;
use App\Core\Components\ProfessionalDevelopment\Repository\ProfessionalDevelopmentGoalRepositoryInterface;
use App\Core\Components\ProfessionalDevelopment\Repository\ProfessionalDevelopmentSubGoalRepositoryInterface;
use InvalidArgumentException;

final class ProfessionalDevelopmentGoalController extends AbstractController
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private JsonResponseService $jsonResponseService,
        private UserTeamRepositoryInterface $userTeamRepository,
        private LogsServiceInterface $logsService,
        private ProfessionalDevelopmentGoalRepositoryInterface $professionalDevelopmentGoalRepository,
        private ProfessionalDevelopmentSubGoalRepositoryInterface $professionalDevelopmentSubGoalRepository,
    ) {
    }

    /**
     * @Route("/api/professional-development-goal", name="create-professional-development-goals", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        $user = $request->attributes->get('user');

        $input = json_decode($request->getContent(), true);
        try {
            $this->checkInputValidity($input);
        } catch (InvalidArgumentException $e) {
            $this->logsService->add(400, LogsContextEnum::PROFESSIONAL_DEVELOPMENT, LogsAlertEnum::WARNING, "InvalidInputException");
            return new JsonResponse('Invalid Input', 400);
        }

        if (isset($input['userId'])) {
            $user = $this->userRepository->findUserById($input['userId']);
        }

        $goal = new ProfessionalDevelopmentGoal(
            $user,
            $input['goal'],
            new \DateTime($input['startDate']),
            new \DateTime($input['endDate'])
        );
        $this->professionalDevelopmentGoalRepository->add($goal);

        return $this->jsonResponseService->create(new GoalViewModel(
            $goal->getId(),
            $goal->getGoal(),
            $goal->getProgression(),
            $goal->getStartDate(),
            $goal->getEndDate(),
            [],
        ), 201);
    }

    /**
     * @Route("/api/professional-development-goal", name="get-professional-development-goals", methods={"GET"})
     */
    public function getProfessionalDevelopmentGoals(Request $request): Response
    {
        $user = $request->attributes->get('user');

        if ($user === null) {
            return $this->jsonResponseService->errorJsonResponse('User not found', 404);
        }

        $goals = $this->professionalDevelopmentGoalRepository->getByUser($user);

        if (empty($goals)) {
            return $this->jsonResponseService->create([], 200);
        }

        $goalsViewModel = [];

        foreach ($goals as $goal) {
            $subGoals = $this->professionalDevelopmentSubGoalRepository->getSubGoalsViewModelsByGoal($goal);

            $goalsViewModel[] = new GoalViewModel(
                $goal->getId(),
                $goal->getGoal(),
                $goal->getProgression(),
                $goal->getStartDate(),
                $goal->getEndDate(),
                $subGoals,
            );
        }

        return $this->jsonResponseService->create($goalsViewModel);
    }

    /**
     * @Route("/api/professional-development-goal-user", name="get-professional-development-goals-user", methods={"GET"})
     */
    public function getProfessionalDevelopmentGoalsByUser(Request $request): Response
    {
        $userId = $request->query->get('userId');

        if (isset($userId)) {
            $user = $this->userRepository->findUserById((int) $userId);
        }

        if ($user === null) {
            return $this->jsonResponseService->errorJsonResponse('User not found', 404);
        }

        $goals = $this->professionalDevelopmentGoalRepository->getByUser($user);

        if (empty($goals)) {
            return $this->jsonResponseService->create([], 200);
        }

        $goalsViewModel = [];

        foreach ($goals as $goal) {
            $subGoals = $this->professionalDevelopmentSubGoalRepository->getSubGoalsViewModelsByGoal($goal);
            $goalsViewModel[] = new GoalViewModel(
                $goal->getId(),
                $goal->getGoal(),
                $goal->getProgression(),
                $goal->getStartDate(),
                $goal->getEndDate(),
                $subGoals,
            );
        }

        return $this->jsonResponseService->create($goalsViewModel);
    }

    private function checkInputValidity(array $input): void
    {
        if (!isset($input['goal']) || !isset($input['startDate']) || !isset($input['endDate'])) {
            throw new InvalidArgumentException('Invalid input');
        }
    }
}
