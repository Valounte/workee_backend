<?php

namespace App\Client\Controller\ProfessionalDevelopment;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Core\Components\Logs\Entity\Enum\LogsAlertEnum;
use App\Core\Components\Logs\Entity\Enum\LogsContextEnum;
use App\Core\Components\Logs\Services\LogsServiceInterface;
use App\Infrastructure\Response\Services\JsonResponseService;
use App\Core\Components\User\Repository\UserRepositoryInterface;
use App\Infrastructure\User\Exceptions\UserPermissionsException;
use App\Infrastructure\User\Services\CheckUserPermissionsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Core\Components\User\Repository\UserTeamRepositoryInterface;
use App\Core\Components\ProfessionalDevelopment\Entity\Enum\GoalStatusEnum;
use App\Core\Components\ProfessionalDevelopment\Entity\ProfessionalDevelopmentGoal;
use App\Core\Components\ProfessionalDevelopment\Entity\ProfessionalDevelopmentSubGoal;
use App\Core\Components\ProfessionalDevelopment\Repository\ProfessionalDevelopmentGoalRepositoryInterface;
use App\Core\Components\ProfessionalDevelopment\Repository\ProfessionalDevelopmentSubGoalRepositoryInterface;

final class ProfessionalDevelopmentSubGoalController extends AbstractController
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private JsonResponseService $jsonResponseService,
        private UserTeamRepositoryInterface $userTeamRepository,
        private LogsServiceInterface $logsService,
        private ProfessionalDevelopmentSubGoalRepositoryInterface $professionalDevelopmentSubGoalRepository,
        private ProfessionalDevelopmentGoalRepositoryInterface $professionalDevelopmentGoalRepository,
    ) {
    }

    /**
     * @Route("/api/professional-development-sub-goal", name="create-professional-development-sub-goal", methods={"POST"})
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

        $subGoal = new ProfessionalDevelopmentSubGoal(
            $input['subGoal'],
            GoalStatusEnum::TO_DO,
            $this->professionalDevelopmentGoalRepository->get($input['goalId'])
        );
        $this->professionalDevelopmentSubGoalRepository->add($subGoal);

        return $this->jsonResponseService->successJsonResponse('subGoal created successfully', 201);
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
        if (!isset($input['subGoal']) || !isset($input['goalId'])) {
            throw new InvalidArgumentException('Invalid input');
        }
    }
}
