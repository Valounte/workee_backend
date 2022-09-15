<?php

namespace App\Core\Components\Notification\UseCase;

use Symfony\Component\Mercure\Update;
use Symfony\Component\Mercure\HubInterface;
use App\Infrastructure\Token\Services\TokenService;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Core\Components\Notification\Entity\Notification;
use App\Core\Components\Notification\UseCase\TestCommand;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Core\Components\Notification\UseCase\NotificationCommand;
use App\Core\Components\Notification\Repository\NotificationRepositoryInterface;

final class TestHandler implements MessageHandlerInterface
{
    public function __construct(
        private NotificationRepositoryInterface $notificationRepository,
        private HubInterface $hub,
        private TokenService $tokenService,
        private MessageBusInterface $messageBus,
        private string $mercureHubUrl,
    ) {
    }

    public function __invoke(TestCommand $command): void
    {
        $myCommand = $command;
        
        $update = new Update(
            $this->mercureHubUrl . '/notification' . '/' . "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwiY29tcGFueSI6MX0.eOe0BAe5N9aCCdwB7ETFrrOpQaHSWJUsejayELA-SmU",
            json_encode([
                "alertLevel" => "important",
                "recepteurId" => 1,
                "message" => "zouzoubizouz"
            ])
        );


        $this->hub->publish($update);
    }
}
