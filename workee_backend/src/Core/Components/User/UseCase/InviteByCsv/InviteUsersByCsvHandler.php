<?php

namespace App\Core\Components\User\UseCase\InviteByCsv;

use Symfony\Component\Messenger\MessageBusInterface;
use App\Infrastructure\FileUploader\Services\FileUploader;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Core\Components\User\UseCase\Register\RegisterUserCommand;
use App\Core\Components\User\UseCase\Register\SendInviteEmailCommand;
use App\Core\Components\User\UseCase\InviteByCsv\InviteUsersByCsvCommand;
use App\Infrastructure\FileUploader\Services\FileContentService;
use App\Infrastructure\User\Exceptions\UserInformationException;

final class InviteUsersByCsvHandler implements MessageHandlerInterface
{
    public function __construct(
        private FileUploader $fileUploader,
        private FileContentService $fileContentService,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function __invoke(InviteUsersByCsvCommand $command): void
    {
        $fileName = $this->fileUploader->upload($command->getFile());

        $fileContent = $this->fileContentService->getUserRegistrationInformationCsvFileContent($fileName);

        foreach ($fileContent as $userRegistrationInformationDto) {
            $registerUserCommand = new RegisterUserCommand(
                $userRegistrationInformationDto->getFirstname(),
                $userRegistrationInformationDto->getLastname(),
                $userRegistrationInformationDto->getEmail(),
                $command->getCompany(),
            );

            try {
                $this->messageBus->dispatch($registerUserCommand);
            } catch (UserInformationException $e) {
                continue;
            }

            $this->messageBus->dispatch(new SendInviteEmailCommand($userRegistrationInformationDto->getEmail()));
        }
    }
}
