<?php

namespace App\Core\Components\User\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Core\Components\Company\Entity\Company;
use App\Core\Components\Job\Entity\Job;
use App\Infrastructure\User\Repository\UserRepository;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $email;

    #[ORM\Column(type: 'string', length: 255)]
    private $firstname;

    #[ORM\Column(type: 'string', length: 255)]
    private $lastname;

    #[ORM\Column(type: 'datetime')]
    private $created_at;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $password;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $picture;

    #[ORM\ManyToOne(targetEntity: Company::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $company;

    #[ORM\ManyToOne(targetEntity: Job::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $job;

    public function __construct(
        string $email,
        string $firstname,
        string $lastname,
        Company $company,
        ?Job $job = null,
        ?string $password = null,
        ?string $picture = null,
        DateTime $created_at = new DateTime('now'),
    ) {
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->created_at = $created_at;
        $this->company = $company;
        $this->password = $password;
        $this->picture = $picture;
        $this->job = $job;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get the value of job
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * Set the value of job
     *
     * @return  self
     */
    public function setJob($job)
    {
        $this->job = $job;

        return $this;
    }

    /**
     * Get the value of picture
     */
    public function getPicture(): ?string
    {
        return $this->picture;
    }


    /**
     * Set the value of picture
     *
     * @return  self
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }
}
