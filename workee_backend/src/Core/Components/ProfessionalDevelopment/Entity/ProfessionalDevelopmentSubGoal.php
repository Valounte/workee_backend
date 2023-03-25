<?php

namespace App\Core\Components\ProfessionalDevelopment\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Core\Components\ProfessionalDevelopment\Entity\Enum\GoalStatusEnum;
use App\Core\Components\ProfessionalDevelopment\Entity\ProfessionalDevelopmentGoal;
use App\Infrastructure\ProfessionalDevelopment\Repository\ProfessionalDevelopmentSubGoalRepository;

#[ORM\Entity(repositoryClass: ProfessionalDevelopmentSubGoalRepository::class)]
class ProfessionalDevelopmentSubGoal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $subGoal;

    #[ORM\Column(type: 'goal_status', length: 255)]
    private $subGoalStatus;

    #[ORM\ManyToOne(targetEntity: ProfessionalDevelopmentGoal::class)]
    private $goal;

    public function __construct(string $subGoal, GoalStatusEnum $subGoalStatus, ProfessionalDevelopmentGoal $goal)
    {
        $this->subGoal = $subGoal;
        $this->subGoalStatus = $subGoalStatus;
        $this->goal = $goal;
    }

    /**
     * Get the value of id
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the value of subGoal
     */
    public function getSubGoal(): string
    {
        return $this->subGoal;
    }

    /**
     * Get the value of subGoalStatus
     */
    public function getSubGoalStatus(): GoalStatusEnum
    {
        return $this->subGoalStatus;
    }

    /**
     * Get the value of goal
     */
    public function getGoal(): ProfessionalDevelopmentGoal
    {
        return $this->goal;
    }

    public function isDone(): bool
    {
        return $this->subGoalStatus === GoalStatusEnum::DONE;
    }

    /**
     * Set the value of subGoalStatus
     *
     * @return  self
     */
    public function setSubGoalStatus($subGoalStatus)
    {
        $this->subGoalStatus = $subGoalStatus;

        return $this;
    }

    /**
     * Set the value of subGoal
     *
     * @return  self
     */
    public function setSubGoal($subGoal)
    {
        $this->subGoal = $subGoal;

        return $this;
    }
}
