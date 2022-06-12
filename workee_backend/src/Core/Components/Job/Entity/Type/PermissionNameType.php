<?php

namespace App\Core\Components\Job\Entity\Type;

use App\Infrastructure\Doctrine\Type\AbstractEnumType;
use App\Core\Components\Job\Entity\Enum\PermissionNameEnum;

class PermissionNameType extends AbstractEnumType
{
    public const NAME = 'permissionname';

    public function getName(): string
    {
        return self::NAME;
    }

    public static function getEnumsClass(): string
    {
        return PermissionNameEnum::class;
    }
}
