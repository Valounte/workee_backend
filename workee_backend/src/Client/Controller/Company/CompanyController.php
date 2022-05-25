<?php

namespace App\Client\Controller\Company;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Core\Components\Company\Entity\Company;
use App\Core\Components\Company\Repository\CompanyRepositoryInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Infrastructure\Response\Services\JsonResponseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CompanyController extends AbstractController
{
    public function __construct(
        private JsonResponseService $jsonResponseService,
        private CompanyRepositoryInterface $companyRepository
    ) {
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
