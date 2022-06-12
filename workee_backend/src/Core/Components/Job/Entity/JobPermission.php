<?php

namespace App\Core\Components\Job\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Core\Components\Job\Entity\Job;
use App\Core\Components\Job\Entity\Permission;
use App\Infrastructure\Job\Repository\JobPermissionRepository;

#[ORM\Entity(repositoryClass: JobPermissionRepository::class)]
class JobPermission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Job::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $job;

    #[ORM\ManyToOne(targetEntity: Permission::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $permission;

    public function __construct(Job $job, Permission $permission)
    {
        $this->job = $job;
        $this->permission = $permission;
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
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
     * Get the value of permission
     */
    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * Set the value of permission
     *
     * @return  self
     */
    public function setPermission($permission)
    {
        $this->permission = $permission;

        return $this;
    }
}
