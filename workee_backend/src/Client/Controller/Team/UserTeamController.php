<?php

namespace App\Client\Controller\Team;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Core\Components\User\Service\GetUserService;
use App\Core\Components\Logs\Entity\Enum\LogsAlertEnum;
use App\Core\Components\Logs\Entity\Enum\LogsContextEnum;
use App\Core\Components\Logs\Services\LogsServiceInterface;
use App\Infrastructure\Response\Services\JsonResponseService;
use App\Infrastructure\User\Exceptions\UserPermissionsException;
use App\Infrastructure\User\Services\CheckUserPermissionsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Core\Components\User\Repository\UserTeamRepositoryInterface;

class UserTeamController extends AbstractController
{
    public function __construct(
        private GetUserService $getUserService,
        private JsonResponseService $jsonResponseService,
        private CheckUserPermissionsService $checkUserPermissionsService,
        private UserTeamRepositoryInterface $userTeamRepository,
        private LogsServiceInterface $logsService,
    ) {
    }

    /**
     * @Route("api/user/team", name="getUsersInTeam"),
     * methods("GET")
     */
    public function getUsersInTeam(Request $request): Response
    {
        try {
            $user = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $teamId = $request->query->get('teamId');

        $users = $this->userTeamRepository->findUsersByTeamId($teamId);

        if (empty($users)) {
            $this->logsService->add(404, LogsContextEnum::TEAM, LogsAlertEnum::INFO, "NoUsersInTeamException");
            return $this->jsonResponseService->errorJsonResponse('No users found.', 400);
        }

        $usersViewModels = [];

        foreach ($users as $user) {
            $usersViewModels[] = $this->getUserService->createUserViewModel($user);
        }

        $this->logsService->add(200, LogsContextEnum::TEAM, LogsAlertEnum::INFO);
        return $this->jsonResponseService->create($usersViewModels);
    }
}
