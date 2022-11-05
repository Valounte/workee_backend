<?php

namespace App\Core\Components\EnvironmentMetrics\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Core\Components\User\Entity\User;
use SpecShaper\EncryptBundle\Annotations\Encrypted;
use App\Infrastructure\Notification\Repository\NotificationRepository;
use App\Core\Components\Notification\Entity\Enum\NotificationAlertLevelEnum;
use App\Infrastructure\Notification\Repository\NotificationPreferencesRepository;

#[ORM\Entity(repositoryClass: NotificationPreferencesRepository::class)]
class EnvironmentMetricsPreferences
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity:User::class)]
    private $user;

    #[ORM\Column(type: 'string', length: 255)]
    private $metricType;

    #[ORM\Column(type: 'boolean')]
    private $isDesactivated;

    public function __construct(
        User $user,
        string $metricType,
        bool $isDesactivated = false,
    ) {
        $this->user = $user;
        $this->metricType = $metricType;
        $this->isDesactivated = $isDesactivated;
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Get the value of metricType
     */
    public function getMetricType()
    {
        return $this->metricType;
    }

    /**
     * Get the value of isDesactivated
     */
    public function getIsDesactivated()
    {
        return $this->isDesactivated;
    }

    /**
     * Set the value of isDesactivated
     *
     * @return  self
     */
    public function setIsDesactivated($isDesactivated)
    {
        $this->isDesactivated = $isDesactivated;

        return $this;
    }
}
