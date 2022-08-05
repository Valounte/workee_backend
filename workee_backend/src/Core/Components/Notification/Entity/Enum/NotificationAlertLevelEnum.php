<?php

namespace App\Core\Components\Notification\Entity\Enum;

enum NotificationAlertLevelEnum: string
{
    case NORMAL_ALERT = 'NORMAL_ALERT';
    case IMPORTANT_ALERT = 'IMPORTANT_ALERT';
    case URGENT_ALERT = 'URGENT_ALERT';
}
