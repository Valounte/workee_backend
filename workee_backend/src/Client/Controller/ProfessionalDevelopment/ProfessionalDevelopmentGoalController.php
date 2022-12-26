<?php

namespace App\Client\Controller\ProfessionalDevelopment;

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
use App\Infrastructure\User\Exceptions\UserPermissionsException;
use App\Infrastructure\User\Services\CheckUserPermissionsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Core\Components\User\Repository\UserTeamRepositoryInterface;
use App\Core\Components\ProfessionalDevelopment\Entity\ProfessionalDevelopmentGoal;
use App\Core\Components\ProfessionalDevelopment\Repository\ProfessionalDevelopmentGoalRepositoryInterface;
use InvalidArgumentException;

final class ProfessionalDevelopmentGoalController extends AbstractController
{
    public function __construct(
        private CheckUserPermissionsService $checkUserPermissionsService,
        private UserRepositoryInterface $userRepository,
        private JsonResponseService $jsonResponseService,
        private UserTeamRepositoryInterface $userTeamRepository,
        private LogsServiceInterface $logsService,
        private ProfessionalDevelopmentGoalRepositoryInterface $professionalDevelopmentGoalRepository,
    ) {
    }

    /**
     * @Route("/api/professional-development-goal", name="create-professional-development-goals", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        try {
            $user = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $input = json_decode($request->getContent(), true);
        try {
            $this->checkInputValidity($input);
            $status = $this->mapGoalStatus($input['status']);
        } catch (InvalidArgumentException $e) {
            $this->logsService->add(400, LogsContextEnum::PROFESSIONAL_DEVELOPMENT, LogsAlertEnum::WARNING, "InvalidInputException");
            return new JsonResponse('Invalid Input', 400);
        }

        if (!isset($input['userId'])) {
            $user = $this->userRepository->findUserById($user->getId());
        }

        $goal = new ProfessionalDevelopmentGoal(
            $user,
            $input['goal'],
            $status,
            new \DateTime($input['startDate']),
            new \DateTime($input['endDate'])
        );
        $this->professionalDevelopmentGoalRepository->add($goal);

        return $this->jsonResponseService->successJsonResponse('Goal created successfully', 201);
    }

    private function mapGoalStatus(string $status): GoalStatusEnum
    {
        return match ($status) {
            'TO_DO' => GoalStatusEnum::TO_DO,
            'IN_PROGRESS' => GoalStatusEnum::IN_PROGRESS,
            'DONE' => GoalStatusEnum::DONE,
            default => throw new InvalidArgumentException('Invalid status')
        };
    }

    private function checkInputValidity(array $input): void
    {
        if (!isset($input['goal']) || !isset($input['startDate']) || !isset($input['endDate']) || !isset($input['status'])) {
            throw new InvalidArgumentException('Invalid input');
        }
    }
}
