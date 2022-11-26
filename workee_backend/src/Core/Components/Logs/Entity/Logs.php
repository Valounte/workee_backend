<?php

namespace App\Core\Components\Logs\Entity;

use App\Core\Components\Logs\Entity\Enum\LogsAlertEnum;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Core\Components\User\Entity\User;
use App\Infrastructure\Logs\Repository\LogsRepository;
use App\Core\Components\Logs\Entity\Enum\LogsContextEnum;

#[ORM\Entity(repositoryClass: LogsRepository::class)]
class Logs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $code;

    #[ORM\Column(type: 'logs_context_type', length: 255)]
    private $context;

    #[ORM\Column(type: 'string', nullable: true)]
    private $exceptionString;

    #[ORM\Column(type: 'logs_alert_type', length: 255, nullable: true)]
    private $alert;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $user;

    #[ORM\Column(type: 'datetime')]
    private $created_at;

    public function __construct(
        int $code,
        LogsContextEnum $context,
        ?LogsAlertEnum $alert = null,
        ?string $exceptionString = null,
        ?User $user = null,
        DateTime $created_at = new DateTime('now'),
    ) {
        $this->user = $user;
        $this->alert = $alert;
        $this->context = $context;
        $this->code = $code;
        $this->exceptionString = $exceptionString;
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
     * Get the value of code
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Get the value of exception
     */
    public function getException()
    {
        return $this->exceptionString;
    }

    /**
     * Get the value of user
     */
    public function getUser()
    {
        return $this->user;
    }


    /**
     * Get the value of context
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Get the value of created_at
     */
    public function getCreated_at()
    {
        return $this->created_at;
    }

    /**
     * Get the value of alert
     */
    public function getAlert()
    {
        return $this->alert;
    }
}
