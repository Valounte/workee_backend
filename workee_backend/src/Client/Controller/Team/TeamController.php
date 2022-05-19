<?php

namespace App\Client\Controller\Team;

use App\Core\Entity\Team;
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
     * @Route("/api/team", name="login", methods={"POST"})
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
            $response = TeamViewModel::createByTeam($team);
        }

        return new JsonResponse($response);
    }
}
