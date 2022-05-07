<?php

namespace App\Core\Entity;

use App\Core\Entity\Team;
use App\Core\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use App\Infrastructure\Repository\UserTeamRepository;

#[ORM\Entity(repositoryClass: UserTeamRepository::class)]
class UserTeam
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity:User::class)]
    private $user;

    #[ORM\ManyToOne(targetEntity:Team::class)]
    private $team;

    public function __construct(User $user, Team $team)
    {
        $this->user = $user;
        $this->team = $team;
    }

    /**
     * Get the value of user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set the value of user
     *
     * @return  self
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the value of team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set the value of team
     *
     * @return  self
     */
    public function setTeam($team)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }
}
