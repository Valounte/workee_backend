<?php

namespace App\Core\Components\Logs\Services;

use App\Core\Components\User\Entity\User;
use App\Core\Components\Logs\Entity\Enum\LogsAlertEnum;
use App\Core\Components\Logs\Entity\Enum\LogsContextEnum;

interface LogsServiceInterface
{
    public function add(int $code, LogsContextEnum $context, ?LogsAlertEnum $alert = null, ?string $exception = null, ?User $user = null): void;
}
