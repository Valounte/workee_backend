<?php

namespace App\Client\Controller\ProfessionalDevelopment;

use App\Client\ViewModel\ProfessionalDevelopment\SubGoalViewModel;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
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
use App\Core\Components\ProfessionalDevelopment\UseCase\SubGoalHasBeenUpdatedEvent;
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
        private MessageBusInterface $messageBus,
    ) {
    }

    /**
     * @Route("/api/professional-development-sub-goal", name="create-professional-development-sub-goal", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        $input = json_decode($request->getContent(), true);
        try {
            $this->checkInputValidity($input);
        } catch (InvalidArgumentException) {
            $this->logsService->add(400, LogsContextEnum::PROFESSIONAL_DEVELOPMENT, LogsAlertEnum::WARNING, "InvalidInputException");
            return new JsonResponse('Invalid Input', 400);
        }

        $subGoal = new ProfessionalDevelopmentSubGoal(
            $input['subGoal'],
            GoalStatusEnum::TO_DO,
            $this->professionalDevelopmentGoalRepository->get($input['goalId'])
        );
        $this->professionalDevelopmentSubGoalRepository->add($subGoal);

        $event = new SubGoalHasBeenUpdatedEvent($subGoal->getId(), $subGoal->getGoal()->getId());
        $this->messageBus->dispatch($event);

        return $this->jsonResponseService->create(new SubGoalViewModel(
            $subGoal->getId(),
            $subGoal->getSubGoal(),
            $subGoal->getSubGoalStatus()
        ), 201);
    }

    /**
     * @Route("/api/professional-development-sub-goal", name="edit-professional-development-sub-goal", methods={"PUT"})
     */
    public function editSubGoal(Request $request): Response
    {
        $input = json_decode($request->getContent(), true);

        $subGoal = $this->professionalDevelopmentSubGoalRepository->get($input['subGoalId']);

        try {
            $subGoal->setSubGoalStatus($this->mapGoalStatus($input['status']));
        } catch (InvalidArgumentException) {
            return $this->jsonResponseService->errorJsonResponse('Invalid Status', 401);
        }

        $this->professionalDevelopmentSubGoalRepository->add($subGoal);

        $event = new SubGoalHasBeenUpdatedEvent($subGoal->getId(), $subGoal->getGoal()->getId());
        $this->messageBus->dispatch($event);

        return $this->jsonResponseService->successJsonResponse('SubGoal updated', 201);
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
