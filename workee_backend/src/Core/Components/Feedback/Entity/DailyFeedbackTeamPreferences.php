<?php

namespace App\Core\Components\Feedback\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Core\Components\Team\Entity\Team;
use App\Infrastructure\Feedback\Repository\DailyFeedbackTeamPreferencesRepository;

#[ORM\Entity(repositoryClass: DailyFeedbackTeamPreferencesRepository::class)]
class DailyFeedbackTeamPreferences
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string')]
    private $sendingTime;

    #[ORM\ManyToOne(targetEntity: Team::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $team;

    public function __construct(
        string $sendingTime,
        Team $team,
    ) {
        $this->sendingTime = $sendingTime;
        $this->team = $team;
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of sendingTime
     */
    public function getSendingTime()
    {
        return $this->sendingTime;
    }

    /**
     * Get the value of team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set the value of sendingTime
     *
     * @return  self
     */
    public function setSendingTime($sendingTime)
    {
        $this->sendingTime = $sendingTime;

        return $this;
    }
}
