<?php

namespace App\Client\Controller\Job;

use App\Client\ViewModel\Job\JobViewModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Client\ViewModel\Job\PermissionViewModel;
use App\Client\ViewModel\Company\CompanyViewModel;
use App\Core\Components\Job\Repository\JobPermissionRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Infrastructure\Response\Services\JsonResponseService;
use App\Core\Components\Job\Repository\JobRepositoryInterface;
use App\Infrastructure\User\Exceptions\UserPermissionsException;
use App\Infrastructure\User\Services\CheckUserPermissionsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class JobController extends AbstractController
{
    public function __construct(
        private CheckUserPermissionsService $checkUserPermissionsService,
        private JobRepositoryInterface $jobRepository,
        private JsonResponseService $jsonResponseService,
        private JobPermissionRepositoryInterface $jobPermissionRepository,
    ) {
    }

    /**
     * @Route("/api/jobs", name="getJobs", methods={"GET"})
     */
    public function getJobs(Request $request): Response
    {
        try {
            $user = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $jobs = $this->jobRepository->findByCompany($user->getCompany());
        $jobViewModel = [];

        foreach ($jobs as $job) {
            $permissions = $this->jobPermissionRepository->findPermissionsByJob($job);
            $permissionsViewModels = $this->getPermissionsViewModels($permissions);

            $jobViewModel[] = new JobViewModel(
                $job->getId(),
                $job->getName(),
                new CompanyViewModel(
                    $job->getCompany()->getId(),
                    $job->getCompany()->getCompanyName(),
                ),
                $permissionsViewModels,
            );
        }

        return $this->jsonResponseService->create($jobViewModel);
    }

    private function getPermissionsViewModels(array $permissions): array
    {
        $permissionViewModels = [];
        foreach ($permissions as $permission) {
            array_push(
                $permissionViewModels,
                new PermissionViewModel($permission->getId(), $permission->getName(), $permission->getContext())
            );
        }
        return $permissionViewModels;
    }
}
