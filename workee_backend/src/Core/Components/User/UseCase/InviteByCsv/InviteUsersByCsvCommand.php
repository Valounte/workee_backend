<?php

namespace App\Core\Components\User\UseCase\InviteByCsv;

use App\Core\Components\Company\Entity\Company;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class InviteUsersByCsvCommand
{
    public function __construct(
        private UploadedFile $file,
        private Company $company,
    ) {
    }

    /**
     * Get the value of file
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Get the value of company
     */
    public function getCompany()
    {
        return $this->company;
    }
}
