<?php

namespace App\Core\Components\Notification\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Core\Components\User\Entity\User;
use SpecShaper\EncryptBundle\Annotations\Encrypted;
use App\Infrastructure\Notification\Repository\NotificationRepository;
use App\Core\Components\Notification\Entity\Enum\NotificationAlertLevelEnum;
use App\Infrastructure\Notification\Repository\NotificationPreferencesRepository;

#[ORM\Entity(repositoryClass: NotificationPreferencesRepository::class)]
class NotificationPreferences
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity:User::class)]
    private $user;

    #[ORM\Column(type: 'notification_alert_level_type', length: 255)]
    private $alertLevel;

    #[ORM\Column(type: 'boolean')]
    private $isMute;

    public function __construct(
        User $user,
        NotificationAlertLevelEnum $alertLevel,
        bool $isMute = false,
    ) {
        $this->user = $user;
        $this->alertLevel = $alertLevel;
        $this->isMute = $isMute;
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
     * Get the value of alertLevel
     */
    public function getAlertLevel()
    {
        return $this->alertLevel;
    }

    /**
     * Get the value of isMute
     */
    public function getIsMute()
    {
        return $this->isMute;
    }

    /**
     * Set the value of isMute
     *
     * @return  self
     */
    public function setIsMute($isMute)
    {
        $this->isMute = $isMute;

        return $this;
    }
}
