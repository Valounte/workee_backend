<?php

namespace App\Core\Components\TeaOrCoffeeMeeting\Repository;

use App\Core\Components\TeaOrCoffeeMeeting\Entity\TeaOrCoffeeMeeting;

interface TeaOrCoffeeMeetingRepositoryInterface
{
    public function add(TeaOrCoffeeMeeting $entity, bool $flush = true): void;
    public function remove(TeaOrCoffeeMeeting $entity, bool $flush = true): void;
}
