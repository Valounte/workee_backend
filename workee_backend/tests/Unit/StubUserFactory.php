<?php

namespace App\Tests\Unit;

use DateTime;
use App\Core\Components\Job\Entity\Job;
use App\Core\Components\User\Entity\User;
use App\Core\Components\Company\Entity\Company;

class StubUserFactory extends User
{
    private int $id;

    public function __construct(
        int $id,
        string $email,
        string $firstname,
        string $lastname,
        Company $company,
        ?Job $job = null,
        ?string $password = null,
        ?string $picture = null,
        DateTime $created_at = null
    ) {
        $this->id = $id;
        parent::__construct($email, $firstname, $lastname, $company, $job, $password, $picture, $created_at ?? new DateTime('now'));
    }

    public static function create(
        int $id,
        string $email = 'test@gmail.com',
        string $firstname = 'test',
        string $lastname = 'test',
        Company $company = new Company('test'),
        ?Job $job = null,
        ?string $password = null,
        ?string $picture = null,
        DateTime $created_at = null
    ): self {
        return new self($id, $email, $firstname, $lastname, $company, $job, $password, $picture, $created_at);
    }

    public function getId(): int
    {
        return $this->id;
    }
}
