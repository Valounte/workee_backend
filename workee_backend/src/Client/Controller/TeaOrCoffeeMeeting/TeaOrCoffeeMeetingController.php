<?php

namespace App\Client\Controller\TeaOrCoffeeMeeting;

use App\Core\Components\User\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Core\Components\Logs\Entity\Enum\LogsAlertEnum;
use App\Core\Components\Logs\Entity\Enum\LogsContextEnum;
use App\Core\Components\Logs\Services\LogsServiceInterface;
use App\Infrastructure\Response\Services\JsonResponseService;
use App\Infrastructure\User\Exceptions\UserNotFoundException;
use App\Core\Components\Team\Repository\TeamRepositoryInterface;
use App\Infrastructure\User\Exceptions\UserPermissionsException;
use App\Infrastructure\User\Services\CheckUserPermissionsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Core\Components\TeaOrCoffeeMeeting\Entity\TeaOrCoffeeMeetingUser;
use App\Core\Components\TeaOrCoffeeMeeting\Entity\Enum\InvitationStatusEnum;
use App\Core\Components\TeaOrCoffeeMeeting\UseCase\CreateTeaOrCoffeeMeetingCommand;
use App\Core\Components\TeaOrCoffeeMeeting\UseCase\CreateTeamTeaOrCoffeeMeetingCommand;
use App\Core\Components\TeaOrCoffeeMeeting\Repository\TeaOrCoffeeMeetingRepositoryInterface;
use App\Core\Components\TeaOrCoffeeMeeting\UseCase\CreateRandomInTeamTeaOrCoffeeMeetingCommand;
use App\Core\Components\TeaOrCoffeeMeeting\Repository\TeaOrCoffeeMeetingUserRepositoryInterface;

final class TeaOrCoffeeMeetingController extends AbstractController
{
    public function __construct(
        private TeaOrCoffeeMeetingRepositoryInterface $teaOrCoffeeMeetingRepository,
        private JsonResponseService $jsonResponseService,
        private CheckUserPermissionsService $checkUserPermissionsService,
        private MessageBusInterface $messageBus,
        private TeamRepositoryInterface $teamRepository,
        private TeaOrCoffeeMeetingUserRepositoryInterface $teaOrCoffeeMeetingUserRepository,
        private LogsServiceInterface $logsService,
    ) {
    }

    /**
     * @Route("/api/tea-or-coffee-meeting", name="createTeaOrCoffeeMeeting", methods={"POST"})
     */
    public function createTeaOrCoffeeMeeting(Request $request): Response
    {
        try {
            $user = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException | UserNotFoundException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $teaOrCoffeeMeetingInput = json_decode($request->getContent(), true);

        $this->dispatchAppropriateCommand($user, $teaOrCoffeeMeetingInput);

        $this->logsService->add(200, LogsContextEnum::TEA_OR_COFFEE_MEETING, LogsAlertEnum::INFO);
        return $this->jsonResponseService->successJsonResponse('Tea or coffee meeting created', 201);
    }

    /**
     * @Route("/api/tea-or-coffee-meeting-invitation-status", name="changeTeaOrCoffeeInvitationStatus", methods={"PUT"})
     */
    public function changeTeaOrCoffeeInvitationStatus(Request $request): Response
    {
        try {
            $user = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException | UserNotFoundException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $input = json_decode($request->getContent(), true);
        $meeting = $this->teaOrCoffeeMeetingUserRepository->findById($input['invitationId']);

        if ($meeting->getInvitedUser()->getId() !== $user->getId()) {
            $this->logsService->add(401, LogsContextEnum::TEA_OR_COFFEE_MEETING, LogsAlertEnum::CRITIC, "NotAllowedException");
            return new JsonResponse('You are not allowed to change invitation status', 403);
        }

        $invitationStatus = match ($input['invitationStatus']) {
            'ACCEPTED' => InvitationStatusEnum::ACCEPTED,
            'DECLINED' => InvitationStatusEnum::DECLINED,
            'PENDING' => InvitationStatusEnum::PENDING,
            default => throw new \InvalidArgumentException('Invalid invitation status'),
        };

        $meeting->setInvitationStatus($invitationStatus);
        $this->teaOrCoffeeMeetingUserRepository->add($meeting);

        $this->logsService->add(401, LogsContextEnum::TEA_OR_COFFEE_MEETING, LogsAlertEnum::INFO);
        return $this->jsonResponseService->successJsonResponse('Invitation status changed', 200);
    }

    /**
     * @Route("/api/tea-or-coffee-meeting-initiator", name="getTeaOrCoffeeMettingsWhereIAmInitiator", methods={"GET"})
     */
    public function getTeaOrCoffeeMettingsWhereIAmInitiator(Request $request): Response
    {
        try {
            $user = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException | UserNotFoundException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $meetings = $this->teaOrCoffeeMeetingUserRepository->getAllTeaOrCoffeeMeetingByInitiator($user);

        if (empty($meetings)) {
            $this->logsService->add(404, LogsContextEnum::TEA_OR_COFFEE_MEETING, LogsAlertEnum::INFO, "NoMeetingsFoundException");
            return $this->jsonResponseService->successJsonResponse('You have no meetings', 200);
        }

        $this->logsService->add(200, LogsContextEnum::TEA_OR_COFFEE_MEETING, LogsAlertEnum::INFO);
        return $this->jsonResponseService->create($meetings, 200);
    }

    /**
     * @Route("/api/tea-or-coffee-meeting", name="getTeaOrCoffeeMettings", methods={"GET"})
     */
    public function getTeaOrCoffeeMettings(Request $request): Response
    {
        try {
            $user = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException | UserNotFoundException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $invitationStatus = $request->query->get('invitationStatus');

        $invitationstatus = match ($invitationStatus) {
            'ACCEPTED' => InvitationStatusEnum::ACCEPTED,
            'DECLINED' => InvitationStatusEnum::DECLINED,
            'PENDING' => InvitationStatusEnum::PENDING,
            default => throw new \InvalidArgumentException('Invalid invitation status'),
        };

        $meetings = $this->teaOrCoffeeMeetingUserRepository->getAllTeaOrCoffeeMeetingByUser($user, $invitationstatus);

        if (empty($meetings)) {
            $this->logsService->add(404, LogsContextEnum::TEA_OR_COFFEE_MEETING, LogsAlertEnum::INFO, "NoMeetingsFoundException");
            return $this->jsonResponseService->successJsonResponse('You have no meetings', 200);
        }

        $this->logsService->add(200, LogsContextEnum::TEA_OR_COFFEE_MEETING, LogsAlertEnum::INFO);
        return $this->jsonResponseService->create($meetings, 200);
    }

    private function dispatchAppropriateCommand(User $user, array $teaOrCoffeeMeetingInput): void
    {
        if ($teaOrCoffeeMeetingInput["meetingType"] === "CLASSIC") {
            $command = new CreateTeaOrCoffeeMeetingCommand(
                $user,
                $teaOrCoffeeMeetingInput["invitedUsersIds"],
                new \DateTime($teaOrCoffeeMeetingInput["date"]),
            );
            $this->messageBus->dispatch($command);
            return;
        }

        $team = $this->teamRepository->findOneById($teaOrCoffeeMeetingInput["teamId"]);

        if ($teaOrCoffeeMeetingInput["meetingType"] === "RANDOM_IN_TEAM") {
            $command = new CreateRandomInTeamTeaOrCoffeeMeetingCommand(
                $user,
                $team,
                new \DateTime($teaOrCoffeeMeetingInput["date"]),
            );
            $this->messageBus->dispatch($command);
            return;
        }

        if ($teaOrCoffeeMeetingInput["meetingType"] === "TEAM") {
            $command = new CreateTeamTeaOrCoffeeMeetingCommand(
                $user,
                $team,
                new \DateTime($teaOrCoffeeMeetingInput["date"]),
            );
            $this->messageBus->dispatch($command);

            $this->logsService->add(400, LogsContextEnum::TEA_OR_COFFEE_MEETING, LogsAlertEnum::CRITIC, "InvalidInputException");
            return;
        }
    }
}
