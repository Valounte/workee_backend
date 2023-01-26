<?php

namespace App\Core\Components\TeaOrCoffeeMeeting\Repository;

use App\Core\Components\User\Entity\User;
use App\Core\Components\TeaOrCoffeeMeeting\Entity\TeaOrCoffeeMeetingUser;
use App\Core\Components\TeaOrCoffeeMeeting\Entity\Enum\InvitationStatusEnum;

interface TeaOrCoffeeMeetingUserRepositoryInterface
{
    public function add(TeaOrCoffeeMeetingUser $entity, bool $flush = true): void;
    public function remove(TeaOrCoffeeMeetingUser $entity, bool $flush = true): void;
    public function findById(int $id): ?TeaOrCoffeeMeetingUser;
    public function getAllTeaOrCoffeeMeetingByUser(User $user): ?array;
    public function getAllTeaOrCoffeeMeetingByInitiator(User $user): ?array;
    public function getAllTeaOrCoffeeMeetingsInTenMinutes(): ?array;
}
