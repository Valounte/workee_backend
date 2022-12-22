<?php

namespace App\Core\Components\Feedback\Entity;

use App\Core\Components\User\Entity\User;
use App\Core\Components\Feedback\Repository\PersonalFeedbackRepositoryInterface;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonalFeedbackRepositoryInterface::class)]
class PersonalFeedback 
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $sender;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $receiver;

    #[ORM\Column(type: 'string', nullable: 'true')]
    private $message;

    #[ORM\Column(type: 'datetime')]
    private $created_at;

    public function __construct(
        User $sender,
        User $receiver,
        ?string $message,
        DateTime $created_at = new DateTime('now'),
    )
    {
        $this->sender = $sender;
        $this->receiver = $receiver;
        $this->message = $message;
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
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of sender
     */ 
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set the value of sender
     *
     * @return  self
     */ 
    public function setSender($sender)
    {
        $this->sender = $sender;

        return $this;
    }
    

    /**
     * Get the value of receiver
     */ 
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * Set the value of receiver
     *
     * @return  self
     */ 
    public function setReceiver($receiver)
    {
        $this->receiver = $receiver;

        return $this;
    }

    /**
     * Get the value of message
     */ 
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set the value of message
     *
     * @return  self
     */ 
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get the value of created_at
     */ 
    public function getCreated_at()
    {
        return $this->created_at;
    }

    /**
     * Set the value of created_at
     *
     * @return  self
     */ 
    public function setCreated_at($created_at)
    {
        $this->created_at = $created_at;

        return $this;
    }
}
