<?php

namespace App\Infrastructure\Response\Services;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

final class JsonResponseService
{
    public function __construct(private SerializerInterface $serializer)
    {
    }

    public function successJsonResponse(string $message, int $code): JsonResponse
    {
        return new JsonResponse(['message' => $message], $code);
    }

    public function errorJsonResponse(string $message, int $code): JsonResponse
    {
        return new JsonResponse(['message' => $message], $code);
    }

    public function create($data, int $status = 200, array $headers = []): JsonResponse
    {
        if (is_array($data)) {
            $result = array_map(function ($element) {
                $serialized = $this->serializer->serialize($element, JsonEncoder::FORMAT);
                return json_decode($serialized);
            }, $data);
        } else {
            $serialized = $this->serializer->serialize($data, JsonEncoder::FORMAT);
            $result = json_decode($serialized);
        }

        return new JsonResponse(
            $result,
            $status,
        );
    }
}
