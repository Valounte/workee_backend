<?php

namespace App\Client\Controller\Feedback;

use Symfony\Component\Routing\Annotation\Route;
use App\Client\ViewModel\User\PersonalFeedbackSenderViewModel;
use App\Core\Components\Feedback\Entity\PersonalFeedback;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Core\Components\User\Service\GetUserService;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Core\Components\Logs\Entity\Enum\LogsAlertEnum;
use App\Core\Components\Logs\Entity\Enum\LogsContextEnum;
use App\Core\Components\Logs\Services\LogsServiceInterface;
use App\Client\ViewModel\Feedback\PersonalFeedbackViewModel;
use App\Infrastructure\Response\Services\JsonResponseService;
use App\Core\Components\User\Repository\UserRepositoryInterface;
use App\Infrastructure\User\Exceptions\UserPermissionsException;
use App\Infrastructure\User\Services\CheckUserPermissionsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Core\Components\Feedback\Repository\PersonalFeedbackRepositoryInterface;

final class PersonalFeedbackController extends AbstractController
{
    public function __construct(
        private JsonResponseService $jsonResponseService,
        private PersonalFeedbackRepositoryInterface $personalFeedbackRepository,
        private CheckUserPermissionsService $checkUserPermissionsService,
        private UserRepositoryInterface $userRepository,
        private LogsServiceInterface $logsService,
    ) {
    }
    /**
     * @Route("/api/send-personal-feedback", name="sendPersonalFeedback", methods={"POST"})
     */
    public function sendFeedback(Request $request): Response
    {
        try {
            $user = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $input = json_decode($request->getContent(), true);

        if (!isset($input['message']) || !isset($input["receiver"])) {
            $this->logsService->add(400, LogsContextEnum::PERSONAL_FEEDBACK, LogsAlertEnum::WARNING, 'InvalidInputException');
            return new JsonResponse('Message and receiver are required', 400);
        }

        $receiver = $this->userRepository->findUserById($input["receiver"]);

        $personalFeedback = new PersonalFeedback(
            $user,
            $receiver,
            $input["message"],
        );
        $this->personalFeedbackRepository->add($personalFeedback);

        return $this->jsonResponseService->successJsonResponse('Personal feedback sent', 200);
    }

    /**
     * @Route("/api/get-personal-feedback", name="getPersonalFeedback", methods={"GET"})
     */
    public function getUserPersonalFeedback(Request $request): Response
    {
        try {
            $user = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $limit = json_decode($request->query->get("limit"), true);

        $personalFeedbackList = $this->personalFeedbackRepository->findByReceiver($user, $limit);

        if (empty($personalFeedbackList)) {
            $this->logsService->add(404, LogsContextEnum::PERSONAL_FEEDBACK, LogsAlertEnum::WARNING, 'PersonalFeedbackNotFoundException');
            return new JsonResponse('No personal feedback', 404);
        }

        $userPersonalFeedback = [];
        foreach ($personalFeedbackList as $personalFeedback) {
            $userPersonalFeedback[] = new PersonalFeedbackViewModel(
                $personalFeedback->getId(),
                new PersonalFeedbackSenderViewModel(
                    $user->getFirstname(),
                    $user->getLastname(),
                ),
                $personalFeedback->getMessage(),
                $personalFeedback->getCreated_At(),
            );
        }
        $this->logsService->add(200, LogsContextEnum::DAILY_FEEDBACK, LogsAlertEnum::INFO);
        return $this->jsonResponseService->create($userPersonalFeedback, 200);
    }
}
