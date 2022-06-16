<?php

namespace App\Core\Components\Job\Entity\Type;

use App\Infrastructure\Doctrine\Type\AbstractEnumType;
use App\Core\Components\Job\Entity\Enum\PermissionContextEnum;

class PermissionContextType extends AbstractEnumType
{
    public const NAME = 'permission_context';

    public function getName(): string
    {
        return self::NAME;
    }

    public static function getEnumsClass(): string
    {
        return PermissionContextEnum::class;
    }
}
