<?php

namespace App\Client\Controller\User;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Infrastructure\Token\Services\TokenService;
use App\Core\Components\User\Service\GetUserService;
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
        private CheckUserPermissionsService $checkUserPermissionsService,
    ) {
    }

    /**
     * @Route("api/me", name="get_me"),
     * methods("GET")
     */
    public function me(Request $request): JsonResponse
    {
        try {
            $jwt = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $user = $this->getUserService->getUserViewModelById($jwt["id"]);

        return new JsonResponse($user);
    }
}
