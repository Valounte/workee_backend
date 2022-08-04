<?php

namespace App\Infrastructure\Token\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Core\Components\User\Entity\User;
use Symfony\Component\HttpFoundation\Request;

final class TokenService
{
    public function decode(Request $request): array
    {
        $authorizationHeader = $request->headers->get('Authorization');
        $authorizationHeaderArray = explode(' ', $authorizationHeader);
        $token = $authorizationHeaderArray[1] ?? null;
        try {
            $jwt = JWT::decode($token, new Key('jwt_secret', 'HS256'));
        } catch (\Exception $e) {
            throw new \Exception('Invalid token');
        }
        $decoded_array = (array) $jwt;
        return $decoded_array;
    }

    public function createLoginToken(User $user): string
    {
        $token = JWT::encode(
            [
                'id' => $user->getId(),
                'company' => $user->getCompany()->getId(),
            ],
            'jwt_secret',
            'HS256'
        );

        return $token;
    }

    public function create(array $payload): string
    {
        $token = JWT::encode($payload, 'jwt_secret', 'HS256');
        return $token;
    }
}
