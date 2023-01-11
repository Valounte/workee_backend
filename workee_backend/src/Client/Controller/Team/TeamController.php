<?php

namespace App\Client\Controller\Team;

use App\Core\Components\Team\Entity\Team;
use App\Client\ViewModel\Team\TeamViewModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Client\ViewModel\Company\CompanyViewModel;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Core\Components\Logs\Entity\Enum\LogsAlertEnum;
use App\Core\Components\Logs\Entity\Enum\LogsContextEnum;
use App\Core\Components\Logs\Services\LogsServiceInterface;
use App\Infrastructure\Response\Services\JsonResponseService;
use App\Core\Components\Team\Repository\TeamRepositoryInterface;
use App\Infrastructure\User\Exceptions\UserPermissionsException;
use App\Infrastructure\User\Services\CheckUserPermissionsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Core\Components\Company\Repository\CompanyRepositoryInterface;

class TeamController extends AbstractController
{
    public function __construct(
        private TeamRepositoryInterface $teamRepository,
        private JsonResponseService $jsonResponseService,
        private CompanyRepositoryInterface $companyRepository,
        private LogsServiceInterface $logsService,
    ) {
    }

    /**
     * @Route("/api/team", name="createTeam", methods={"POST"})
     */
    public function createTeam(Request $request): Response
    {
        $user = $request->attributes->get('user');

        $teamData = json_decode($request->getContent(), true);

        if (!isset($teamData['name']) || !isset($teamData['description'])) {
            $this->logsService->add(400, LogsContextEnum::TEAM, LogsAlertEnum::WARNING, "InvalidInputException");
            return new JsonResponse("Team name is empty", 400);
        }

        $team = new Team(
            $teamData["name"],
            $teamData["description"],
            $user->getCompany(),
        );

        $this->teamRepository->add($team);

        $this->logsService->add(200, LogsContextEnum::TEAM, LogsAlertEnum::INFO);
        return $this->jsonResponseService->create(
            [
                "team" => new TeamViewModel($team->getId(), $team->getTeamName(), $team->getDescription(), new CompanyViewModel($team->getCompany()->getId(), $team->getCompany()->getCompanyName())),
                "message" => "Team created successfully."
            ],
            200,
        );
    }

    /**
     * @Route("/api/teams", name="listTeams", methods={"GET"})
     */
    public function listTeams(Request $request): Response
    {
        $user = $request->attributes->get('user');

        $teams = $this->teamRepository->findTeamsByCompany($user->getCompany());

        $response = array();

        foreach ($teams as $team) {
            array_push($response, new TeamViewModel(
                $team->getId(),
                $team->getTeamName(),
                $team->getDescription(),
                new CompanyViewModel(
                    $team->getCompany()->getId(),
                    $team->getCompany()->getCompanyName(),
                ),
            ));
        }

        $this->logsService->add(200, LogsContextEnum::TEAM, LogsAlertEnum::WARNING);
        return $this->jsonResponseService->create($response);
    }

    /**
     * @Route("/api/team/{id}", name="removeTeam", methods={"DELETE"})
     */
    public function removeTeam(Request $request, $id): Response
    {
        $team = $this->teamRepository->findOneById($id);

        if (!$team) {
            $response = $this->jsonResponseService->errorJsonResponse(
                "No Team Found with the selected ID",
                404
            );
            return $response;
        }

        $this->teamRepository->remove($team);
        return $this->jsonResponseService->successJsonResponse(
            "Team successfully removed !",
            200
        );
    }

    /**
     * @Route("/api/team", name="editTeam", methods={"PUT"})
     */
    public function editTeam(Request $request): Response
    {
        $user = $request->attributes->get('user');

        $teamData = json_decode($request->getContent(), true);
        $team = $this->teamRepository->findOneById($teamData["id"]);

        if (!$team) {
            $this->logsService->add(404, LogsContextEnum::TEAM, LogsAlertEnum::WARNING, "TeamNotFoundException");
            return $this->jsonResponseService->errorJsonResponse(
                "No Team Found with the selected ID",
                404
            );
        }

        $team->setTeamName($teamData["name"]);
        $this->teamRepository->add($team);
        $this->logsService->add(200, LogsContextEnum::TEAM, LogsAlertEnum::INFO);
        return new JsonResponse(
            ["team" => new TeamViewModel(
                $team->getId(),
                $team->getTeamName(),
                $team->getDescription(),
                new CompanyViewModel(
                    $team->getCompany()->getId(),
                    $team->getCompany()->getCompanyName(),
                )
            )],
            200
        );
    }

    /**
     * @Route("/api/team/{id}", name="getTeamById", methods={"get"})
     */
    public function getTeam(Request $request, $id): Response
    {
        $team = $this->teamRepository->findOneById($id);

        if (!$team) {
            $this->logsService->add(404, LogsContextEnum::TEAM, LogsAlertEnum::WARNING, "TeamNotFoundException");
            return $this->jsonResponseService->errorJsonResponse(
                "No Team Found with the selected ID",
                404
            );
        }

        $teamViewModel = new TeamViewModel(
            $team->getId(),
            $team->getTeamName(),
            $team->getDescription(),
            new CompanyViewModel(
                $team->getCompany()->getId(),
                $team->getCompany()->getCompanyName(),
            )
        );

        $this->logsService->add(200, LogsContextEnum::TEAM, LogsAlertEnum::INFO);
        return $this->jsonResponseService->create($teamViewModel);
    }
}
