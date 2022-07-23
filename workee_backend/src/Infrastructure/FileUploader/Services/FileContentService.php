<?php

namespace App\Infrastructure\FileUploader\Services;

use App\Core\Components\User\Dto\UserRegistrationInformationDto;

final class FileContentService
{
    public static function getUserRegistrationInformationCsvFileContent(string $fileName): array
    {
        $doc = fopen($fileName, "r");
        $docContent = [];

        for ($lineNumber = 1; ($raw_string = fgets($doc)) !== false; $lineNumber++) {
            $line = str_getcsv($raw_string);
            $docContent[] = new UserRegistrationInformationDto(
                $line[0],
                $line[1],
                $line[2],
            );
        }

        fclose($doc);
        unlink($fileName);

        return $docContent;
    }
}
