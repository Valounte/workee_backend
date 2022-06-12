<?php

namespace App\Infrastructure\Response\Services;

use App\Client\ViewModel\User\UserViewModel;
use Symfony\Component\HttpFoundation\JsonResponse;

final class JsonResponseService
{
    public function successJsonResponse(string $message, int $code): JsonResponse
    {
        return new JsonResponse(['message' => $message], $code);
    }

    public function errorJsonResponse(string $message, int $code): JsonResponse
    {
        return new JsonResponse(['message' => $message], $code);
    }
}
