<?php

namespace App\Core\Components\Feedback\UseCase;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use App\Core\Components\Feedback\UseCase\TeamNeedsToSendFeedbackEvent;

final class TeamNeedsToSendFeedbackListener implements MessageHandlerInterface {
    public function __construct() {}

    public function __invoke(TeamNeedsToSendFeedbackEvent $teamNeedsToSendFeedback)
    {

    }
}
