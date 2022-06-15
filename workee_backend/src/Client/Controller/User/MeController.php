<?php

namespace App\Client\Controller\User;

use Exception;
use App\Core\Components\User\Service\GetUserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Infrastructure\Token\Services\TokenService;
use App\Infrastructure\Response\Services\JsonResponseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MeController extends AbstractController
{
    public function __construct(
        private JsonResponseService $jsonResponseService,
        private TokenService $tokenService,
        private GetUserService $getUserService,
    ) {
    }

    /**
     * @Route("api/me", name="get_me"),
     * methods("GET")
     */
    public function me(Request $request): JsonResponse
    {
        try {
            $jwt = $this->tokenService->decode($request);
        } catch (Exception $e) {
            return $this->jsonResponseService->errorJsonResponse('Unauthorized', 401);
        }

        $user = $this->getUserService->getUserViewModelById($jwt["id"]);

        return new JsonResponse($user);
    }
}
