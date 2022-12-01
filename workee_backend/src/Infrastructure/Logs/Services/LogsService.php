<?php

namespace App\Infrastructure\Logs\Services;

use App\Core\Components\Logs\Entity\Enum\LogsAlertEnum;
use App\Core\Components\Logs\Entity\Logs;
use App\Core\Components\User\Entity\User;
use App\Core\Components\Logs\Entity\Enum\LogsContextEnum;
use App\Core\Components\Logs\Repository\LogsRepositoryInterface;
use App\Core\Components\Logs\Services\LogsServiceInterface;

final class LogsService implements LogsServiceInterface
{
    public function __construct(
        private LogsRepositoryInterface $logsRepository,
    ) {
    }

    public function add(int $code, LogsContextEnum $context, ?LogsAlertEnum $alert = null, ?string $exception = null, ?User $user = null): void
    {
        $log = new Logs($code, $context, $alert, $exception, $user);

        $this->logsRepository->add($log);
    }
}
