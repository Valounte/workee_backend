<?php

namespace App\Infrastructure\User\Services;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use App\Infrastructure\Token\Services\TokenService;
use App\Core\Components\Job\Entity\Enum\PermissionNameEnum;
use App\Core\Components\User\Repository\UserRepositoryInterface;
use App\Infrastructure\User\Exceptions\UserPermissionsException;
use App\Core\Components\Job\Repository\JobPermissionRepositoryInterface;
use App\Infrastructure\User\Exceptions\UserNotFoundException;

final class CheckUserPermissionsService
{
    public function __construct(
        private UserRepositoryInterface $userRepositoryInterface,
        private JobPermissionRepositoryInterface $jobPermissionRepositoryInterface,
        private TokenService $tokenService,
    ) {
    }

    public function checkUserPermissionsByJwt(Request $request, ?PermissionNameEnum $permission = null): array
    {
        try {
            $jwt = $this->tokenService->decode($request);
        } catch (Exception $e) {
            throw UserPermissionsException::invalidTokenException();
        }

        if ($permission == null) {
            return $jwt;
        }

        $user = $this->userRepositoryInterface->findUserById($jwt['id']);

        if ($user == null) {
            throw new UserNotFoundException();
        }

        $job = $user->getJob();

        if ($job != null) {
            $jobPermissions = $this->jobPermissionRepositoryInterface->findPermissionsByJob($user->getJob());

            foreach ($jobPermissions as $jobPermission) {
                if ($jobPermission->getName() === $permission) {
                    return $jwt;
                }
            }
        }

        throw UserPermissionsException::invalidJobPermissionException();
    }
}
