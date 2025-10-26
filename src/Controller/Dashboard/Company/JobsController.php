<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Company;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Service\CompanyService;
use App\Service\Jobs\CompanyDeletionJob;
use App\Service\ProjectService;
use App\Service\UserService;
use App\Traits\FormValidationTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/company/job/v3c0s6h5z5k7x5rm')]
class JobsController extends AbstractDashboardController
{
    use FormValidationTrait;

    private const string APP_DASHBOARD_COMPANIES_INDEX = 'app_dashboard_companies_index';

    public function __construct(
        private readonly UserService            $userService,
        private readonly CompanyService         $companyService,
        private readonly ProjectService         $projectService,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/delete/company/{id}', name: 'app_dashboard_company_delete_company_and_all_related_data', methods: 'POST')]
    public function delete(?string $id, Request $request, CompanyDeletionJob $companyDeletionJob): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $user = $this->getUser();
        $password = $this->validate($request->request->get('pw', ''));

        if (!$this->userService->isPasswordValid($user, $password)) {
            $this->addFlash('danger', 'Password is not correct. Please type your password to continue.');
            return $this->redirectToRoute(self::APP_DASHBOARD_COMPANIES_INDEX);
        }

        $company = $this->companyService->getById(
            $this->validateNumber($request->request->get('cId')),
        );

        if (!$company || $this->validateNumber($id) !== $company->getId()) {
            $this->addFlash('warning', 'Company could not be found.');
            return $this->redirectToRoute(self::APP_DASHBOARD_COMPANIES_INDEX);
        }

        $companyName = $this->validate($request->request->get('name'));

        if ($companyName !== $company->getName()) {
            $this->addFlash('warning', 'Type company name correctly and try again.');
            return $this->redirectToRoute(self::APP_DASHBOARD_COMPANIES_INDEX);
        }

        $companyDeletionJob->deleteEverythingRelatedToCompany($company, $this->entityManager)
            ? $this->addFlash('success', 'The company and its related data has been successfully deleted.')
            : $this->addFlash('warning', 'The company and its related data could not be deleted.');

        return $this->redirectToRoute(self::APP_DASHBOARD_COMPANIES_INDEX);
    }
}
