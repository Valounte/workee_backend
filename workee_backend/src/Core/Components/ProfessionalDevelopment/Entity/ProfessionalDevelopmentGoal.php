<?php

namespace App\Core\Components\ProfessionalDevelopment\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Core\Components\User\Entity\User;
use App\Core\Components\ProfessionalDevelopment\Entity\Enum\GoalStatusEnum;
use App\Infrastructure\ProfessionalDevelopment\Repository\ProfessionalDevelopmentGoalRepository;

#[ORM\Entity(repositoryClass: ProfessionalDevelopmentGoalRepository::class)]
class ProfessionalDevelopmentGoal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\OneToOne(targetEntity: User::class)]
    private $user;

    #[ORM\Column(type: 'string', length: 255)]
    private $goal;

    #[ORM\Column(type: 'goal_status', length: 255)]
    private $goalStatus;

    #[ORM\Column(type: 'datetime')]
    private $startDate;

    #[ORM\Column(type: 'datetime')]
    private $endDate;

    public function __construct(User $user, string $goal, GoalStatusEnum $goalStatus, DateTime $startDate, DateTime $endDate)
    {
        $this->user = $user;
        $this->goal = $goal;
        $this->goalStatus = $goalStatus;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Get the value of id
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the value of user
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Get the value of goal
     */
    public function getGoal(): string
    {
        return $this->goal;
    }

    /**
     * Get the value of goalStatus
     */
    public function getGoalStatus(): GoalStatusEnum
    {
        return $this->goalStatus;
    }

    /**
     * Get the value of startDate
     */
    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    /**
     * Get the value of endDate
     */
    public function getEndDate(): DateTime
    {
        return $this->endDate;
    }
}
