<?php

namespace App\Core\Components\EnvironmentMetrics\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Core\Components\User\Entity\User;
use App\Infrastructure\Job\Repository\TemperatureMetricRepository;
use DateTime;

#[ORM\Entity(repositoryClass: TemperatureMetricRepository::class)]
class TemperatureMetric
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'float')]
    private $value;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\Column(type: 'datetime')]
    private $created_at;

    public function __construct(
        float $value,
        User $user,
        DateTime $created_at = new DateTime('now'),
    ) {
        $this->user = $user;
        $this->value = $value;
        $this->created_at = $created_at;
    }

    /**
     * Get the value of value
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get the value of user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of created_at
     */
    public function getCreated_at()
    {
        return $this->created_at;
    }
}
