<?php

namespace App\Client\Controller\Job;

use App\Core\Components\Job\Entity\Job;
use App\Client\ViewModel\Job\JobViewModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Client\ViewModel\Job\PermissionViewModel;
use App\Core\Components\Job\Entity\JobPermission;
use App\Client\ViewModel\Company\CompanyViewModel;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Core\Components\Logs\Entity\Enum\LogsAlertEnum;
use App\Core\Components\Logs\Entity\Enum\LogsContextEnum;
use App\Core\Components\Job\Entity\Enum\PermissionNameEnum;
use App\Core\Components\Logs\Services\LogsServiceInterface;
use App\Infrastructure\Response\Services\JsonResponseService;
use App\Core\Components\Job\Repository\JobRepositoryInterface;
use App\Infrastructure\User\Exceptions\UserPermissionsException;
use App\Infrastructure\User\Services\CheckUserPermissionsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Core\Components\Job\Repository\PermissionRepositoryInterface;
use App\Core\Components\Job\Repository\JobPermissionRepositoryInterface;

final class JobController extends AbstractController
{
    public function __construct(
        private CheckUserPermissionsService $checkUserPermissionsService,
        private JobRepositoryInterface $jobRepository,
        private JsonResponseService $jsonResponseService,
        private JobPermissionRepositoryInterface $jobPermissionRepository,
        private PermissionRepositoryInterface $permissionRepository,
        private LogsServiceInterface $logsService,
    ) {
    }

    /**
     * @Route("/api/jobs", name="getAllJobs", methods={"GET"})
     */
    public function getAllJobs(Request $request): Response
    {
        try {
            $user = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request);
        } catch (UserPermissionsException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $jobs = $this->jobRepository->findByCompany($user->getCompany());

        if (empty($jobs)) {
            $this->logsService->add(404, LogsContextEnum::JOB, LogsAlertEnum::WARNING, "NoJobsFoundException");
            return new JsonResponse("No jobs found", 404);
        }

        $jobViewModel = [];

        foreach ($jobs as $job) {
            $permissions = $this->jobPermissionRepository->findPermissionsByJob($job);
            $permissionsViewModels = $this->getPermissionsViewModels($permissions);

            $jobViewModel[] = new JobViewModel(
                $job->getId(),
                $job->getName(),
                $job->getDescription(),
                new CompanyViewModel(
                    $job->getCompany()->getId(),
                    $job->getCompany()->getCompanyName(),
                ),
                $permissionsViewModels,
            );
        }

        $this->logsService->add(200, LogsContextEnum::JOB, LogsAlertEnum::INFO);
        return $this->jsonResponseService->create($jobViewModel);
    }

    /**
     * @Route("/api/job", name="createJob", methods={"POST"})
     */
    public function createJob(Request $request): Response
    {
        try {
            $user = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request, PermissionNameEnum::CREATE_JOB);
        } catch (UserPermissionsException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $data = json_decode($request->getContent(), true);
        $job = $this->jobRepository->findByNameAndCompany($data["name"], $user->getCompany());

        if (!isset($job)) {
            $this->logsService->add(404, LogsContextEnum::JOB, LogsAlertEnum::WARNING, "JobAlreadyExistsException");
            return $this->jsonResponseService->errorJsonResponse("Job with this name already exists", 404);
        }

        if (!isset($data["name"]) || !isset($data["description"])) {
            $this->logsService->add(400, LogsContextEnum::JOB, LogsAlertEnum::WARNING, "InvalidInputException");
            return $this->jsonResponseService->errorJsonResponse("Job input is not valid", 400);
        }

        $job = new Job(
            $data["name"],
            $data["description"],
            $user->getCompany(),
        );

        $this->jobRepository->add($job);

        foreach ($data["permissionsId"] as $permission) {
            $permission = $this->permissionRepository->findOneById($permission);
            $jobPermission = new JobPermission(
                $job,
                $permission,
            );

            $this->jobPermissionRepository->add($jobPermission);
        }

        $this->logsService->add(201, LogsContextEnum::JOB, LogsAlertEnum::INFO);
        return $this->jsonResponseService->successJsonResponse('Job created', 200);
    }

    /**
     * @Route("/api/job", name="modifyJob", methods={"PUT"})
     */
    public function modifyJob(Request $request): Response
    {
        try {
            $user = $this->checkUserPermissionsService->checkUserPermissionsByJwt($request, PermissionNameEnum::CREATE_JOB);
        } catch (UserPermissionsException $e) {
            return new JsonResponse($e->getMessage(), $e->getCode());
        }

        $input = json_decode($request->getContent(), true);

        $job = $this->jobRepository->findOneById($input["jobId"]);

        if (isset($input["name"])) {
            $job->setName($input["name"]);
        }

        $this->jobRepository->add($job);


        if (isset($input["permissionsId"])) {
            $this->jobPermissionRepository->deleteAllPermissionsByJob($job);
            foreach ($input["permissionsId"] as $permission) {
                $permission = $this->permissionRepository->findOneById($permission);
                $jobPermission = new JobPermission(
                    $job,
                    $permission,
                );
                $this->jobPermissionRepository->add($jobPermission);
            }
        }

        $this->logsService->add(200, LogsContextEnum::JOB, LogsAlertEnum::INFO);
        return $this->jsonResponseService->successJsonResponse('Job modified', 200);
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
