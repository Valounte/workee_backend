<?php

namespace App\Core\Components\Feedback\Entity;

use App\Core\Components\Team\Entity\Team;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Core\Components\User\Entity\User;
use App\Infrastructure\Feedback\Repository\DailyFeedbackRepository;

#[ORM\Entity(repositoryClass: DailyFeedbackRepository::class)]
class DailyFeedback
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $satisfactionDegree;

    #[ORM\Column(type: 'string', nullable: true)]
    private $message;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $user;

    #[ORM\ManyToOne(targetEntity: Team::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $team;

    #[ORM\Column(type: 'datetime')]
    private $created_at;

    public function __construct(
        int $satisfactionDegree,
        Team $team,
        ?string $message = null,
        ?User $user = null,
        DateTime $created_at = new DateTime('now'),
    ) {
        $this->user = $user;
        $this->team = $team;
        $this->message = $message;
        $this->satisfactionDegree = $satisfactionDegree;
        $this->created_at = $created_at;
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of satisfactionDegree
     */
    public function getSatisfactionDegree()
    {
        return $this->satisfactionDegree;
    }

    /**
     * Get the value of user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Get the value of team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Get the value of created_at
     */
    public function getCreated_at()
    {
        return $this->created_at;
    }

    /**
     * Get the value of message
     */
    public function getMessage()
    {
        return $this->message;
    }
}
