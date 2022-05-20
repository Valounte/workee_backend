<?php

namespace App\Client\Controller\Company;

use App\Core\Entity\Company;
use App\Core\Services\JsonResponseService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Infrastructure\Repository\CompanyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CompanyController extends AbstractController
{
    public function __construct(private JsonResponseService $jsonResponseService, private CompanyRepository $companyRepository)
    {
    }

    /**
     * @Route("/api/company", name="company", methods={"POST"})
     */
    public function createCompany(Request $request): Response
    {
        $companyData = json_decode($request->getContent(), true);

        $company = new Company(
            $companyData["companyName"],
        );

        $this->companyRepository->add($company);

        return $this->jsonResponseService->successJsonResponse(
            'Company created successfully.',
            201
        );
    }
}
