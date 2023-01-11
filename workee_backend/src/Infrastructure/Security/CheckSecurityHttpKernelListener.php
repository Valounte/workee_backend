<?php

namespace App\Infrastructure\Security;

use Exception;
use App\Core\Components\User\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use App\Infrastructure\Token\Services\TokenService;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Core\Components\User\Repository\UserRepositoryInterface;

final class CheckSecurityHttpKernelListener implements EventSubscriberInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepositoryInterface,
        private TokenService $tokenService,
        private array $unprotectedRoutes,
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        if (in_array($request->getPathInfo(), $this->unprotectedRoutes)) {
            return;
        }

        try {
            $jwt = $this->tokenService->decode($request);
        } catch (Exception $e) {
            return $event->setResponse(new Response('Invalid token', 401));
        }

        $user = $this->getUser($jwt);

        if ($user == null) {
            return $event->setResponse(new Response('User not found', 404));
        }

        $request->attributes->set('user', $user);

        return;
    }

    private function getUser(array $jwt): ?User
    {
        $user = null;

        if (isset($jwt['id'])) {
            $user = $this->userRepositoryInterface->findUserById($jwt['id']);
        } elseif (isset($jwt['email'])) {
            $user = $this->userRepositoryInterface->findUserByEmail($jwt['email']);
        }

        return $user;
    }
}
