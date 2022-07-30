<?php

namespace App\Infrastructure\User\Services;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use App\Infrastructure\Token\Services\TokenService;
use App\Core\Components\Job\Entity\Enum\PermissionNameEnum;
use App\Core\Components\User\Repository\UserRepositoryInterface;
use App\Infrastructure\User\Exceptions\UserPermissionsException;
use App\Core\Components\Job\Repository\JobPermissionRepositoryInterface;
use App\Core\Components\User\Entity\User;
use App\Infrastructure\Token\ValueObject\Jwt;
use App\Infrastructure\Token\ValueObject\UserIdentification;
use App\Infrastructure\Token\ValueObject\UserIdentifier;
use App\Infrastructure\User\Exceptions\UserNotFoundException;

final class CheckUserPermissionsService
{
    public function __construct(
        private UserRepositoryInterface $userRepositoryInterface,
        private JobPermissionRepositoryInterface $jobPermissionRepositoryInterface,
        private TokenService $tokenService,
    ) {
    }

    public function checkUserPermissionsByJwt(Request $request, ?PermissionNameEnum $permission = null): User
    {
        try {
            $jwt = $this->tokenService->decode($request);
        } catch (Exception $e) {
            throw UserPermissionsException::invalidTokenException();
        }

        $user = null;

        if (isset($jwt['id'])) {
            $user = $this->userRepositoryInterface->findUserById($jwt['id']);
        } elseif (isset($jwt['email'])) {
            $user = $this->userRepositoryInterface->findUserByEmail($jwt['email']);
        }

        if ($permission == null) {
            return $user;
        }


        if ($user == null) {
            throw new UserNotFoundException();
        }

        $job = $user->getJob();

        if ($job != null) {
            $jobPermissions = $this->jobPermissionRepositoryInterface->findPermissionsByJob($user->getJob());

            foreach ($jobPermissions as $jobPermission) {
                if ($jobPermission->getName() === $permission) {
                    return $user;
                }
            }
        }

        throw UserPermissionsException::invalidJobPermissionException();
    }
}
