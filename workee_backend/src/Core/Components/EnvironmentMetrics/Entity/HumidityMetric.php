<?php

namespace App\Core\Components\EnvironmentMetrics\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Core\Components\User\Entity\User;
use App\Infrastructure\EnvironmentMetrics\Repository\HumidityMetricRepository;

#[ORM\Entity(repositoryClass: HumidityMetricRepository::class)]
class HumidityMetric
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
     * Get the value of id
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the value of value
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * Get the value of user
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Get the value of created_at
     */
    public function getCreated_at(): DateTime
    {
        return $this->created_at;
    }
}
