<?php

namespace App\Core\Components\Notification\UseCase;

use App\Core\Components\Notification\Repository\NotificationRepositoryInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Core\Components\Notification\UseCase\NotificationCommand;
use App\Infrastructure\Token\Services\TokenService;

final class TestHandler implements MessageHandlerInterface
{
    public function __invoke(TestCommand $command): void
    {
        $message = $command->getmessage();

        // Do something with $message
    }
}
