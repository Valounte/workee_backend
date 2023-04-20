<?php

namespace App\Tests\Unit;

use App\Core\Components\Team\Entity\Team;
use App\Core\Components\Company\Entity\Company;

final class StubTeamFactory extends Team
{
    private int $id;

    public function __construct(
        int $id,
        string $teamName,
        string $description,
        Company $company,
    ) {
        $this->id = $id;
        parent::__construct($teamName, $description, $company);
    }

    public static function create(
        int $id,
        string $teamName = 'test',
        string $description = 'test',
        Company $company = new Company('test'),
    ): self {
        return new self($id, $teamName, $description, $company);
    }

    public function getId(): int
    {
        return $this->id;
    }
}
