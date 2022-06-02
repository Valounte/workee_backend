<?php

namespace App\Tests\Functionnal\Services;

use Firebase\JWT\JWT;
use Symfony\Component\BrowserKit\Cookie;
use App\Core\Components\User\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Infrastructure\User\Repository\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

final class LoginService extends WebTestCase
{
    public static function generateToken(): string
    {
        $userRepository = static::getContainer()->get(UserRepository::class);

        $user = $userRepository->findUserByEmail('workee@gmail.com');

        $jwt = JWT::encode(
            ["id" => $user->getId(), "company" => $user->getCompany()->getId()],
            'jwt_secret',
            'HS256'
        );

        return sprintf('Bearer %s', $jwt);
    }
}
