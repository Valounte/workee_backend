<?php

namespace App\Client\Controller\User;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Infrastructure\Token\Services\TokenService;
use App\Core\Components\User\Service\GetUserService;
use App\Core\Components\Logs\Entity\Enum\LogsAlertEnum;
use App\Core\Components\Logs\Entity\Enum\LogsContextEnum;
use App\Core\Components\Logs\Services\LogsServiceInterface;
use App\Infrastructure\Response\Services\JsonResponseService;
use App\Infrastructure\User\Exceptions\UserPermissionsException;
use App\Infrastructure\User\Services\CheckUserPermissionsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MeController extends AbstractController
{
    public function __construct(
        private JsonResponseService $jsonResponseService,
        private TokenService $tokenService,
        private GetUserService $getUserService,
        private LogsServiceInterface $logsService,
    ) {
    }

    /**
     * @Route("api/me", name="get_me"),
     * methods("GET")
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->attributes->get('user');

        $user = $this->getUserService->createUserViewModel($user);

        $this->logsService->add(200, LogsContextEnum::USER, LogsAlertEnum::INFO);
        return $this->jsonResponseService->create($user);
    }
}
