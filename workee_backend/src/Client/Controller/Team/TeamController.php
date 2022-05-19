<?php

namespace App\Client\Controller\Team;

use App\Core\Entity\Team;
use App\Core\Entity\Company;
use App\Core\Services\JsonResponseService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Infrastructure\Repository\TeamRepository;
use App\Infrastructure\Repository\CompanyRepository;
use App\Client\ViewModel\TeamViewModel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class TeamController extends AbstractController
{
    public function __construct(
        private TeamRepository $teamRepository,
        private JsonResponseService $jsonResponseService,
        private CompanyRepository $companyRepository,
    ) {
    }

    /**
     * @Route("/api/team", name="createTeam", methods={"POST"})
     */
    public function createTeam(Request $request): Response
    {
        $teamData = json_decode($request->getContent(), true);
        $company = $this->companyRepository->findOneById($teamData['companyId']);

        $team = new Team(
            $teamData["teamName"],
            $company,
        );

        $this->teamRepository->add($team);

        return $this->jsonResponseService->successJsonResponse(
            'Team created successfully.'
        );
    }

    /**
    * @Route("/api/teams", name="listTeams", methods={"GET"})
    */
    public function listTeams()
    {
        $teams = $this->teamRepository->findAll();

        $response = array();

        foreach ($teams as $team) {
            array_push($response, TeamViewModel::createByTeam($team));
        }

        return new JsonResponse($response);
    }

    /**
    * @Route("/api/team/{id}", name="removeTeam", methods={"DELETE"})
    */
    public function removeTeam(Request $request, $id): Response
    {
        $team = $this->teamRepository->findOneById($id);

        if (!$team) {
            $response = new JsonResponseService();
            $response = $response->errorJsonResponse("No Team Found with the selected ID", 404);
            return $response;
        }

        $this->teamRepository->remove($team);

        return new Response('Team successfully erased !');
    }

    /**
    * @Route("/api/team/{id}", name="editTeam", methods={"put"})
    */
    public function editTeam(Request $request, $id): Response
    {
        $teamData = json_decode($request->getContent(), true);

        $team = $this->teamRepository->findOneById($id);

        if (!$team) {
            $response = new JsonResponseService();
            $response = $response->errorJsonResponse("No Team Found with the selected ID", 404);
            return $response;
        }

        $team->setTeamName($teamData["teamName"]);

        $this->teamRepository->add($team);

        $response = TeamViewModel::createByTeam($team);
        return new JsonResponse($response);
    }

    /**
    * @Route("/api/team/{id}", name="getTeamById", methods={"get"})
    */
    public function getTeam(Request $request, $id): Response
    {
        $team = $this->teamRepository->findOneById($id);

        if (!$team) {
            $response = new JsonResponseService();
            $response = $response->errorJsonResponse("No Team Found with the selected ID", 404);
            return $response;
        }

        $response = TeamViewModel::createByTeam($team);
        return new JsonResponse($response);
    }
}
