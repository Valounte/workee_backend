<?php

namespace App\Client\ViewModel\Feedback;

use App\Client\ViewModel\User\PersonalFeedbackSenderViewModel;
use DateTime;

final class PersonalFeedbackViewModel
{
    public function __construct(
        private PersonalFeedbackSenderViewModel $sender,
        private string $message,
        private DateTime $createdAt,
    )
    {
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
        public function getCreatedAt()
        {
                return $this->createdAt;
        }

        /**
         * Set the value of created_at
         *
         * @return  self
         */ 
        public function setCreatedAt($created_at)
        {
                $this->createdAt = $created_at;

                return $this;
        }
}