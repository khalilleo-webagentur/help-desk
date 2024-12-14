<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Company;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Entity\Company;
use App\Service\CompanyService;
use App\Service\Core\MonologService;
use App\Service\ProjectService;
use App\Service\UserService;
use App\Traits\FormValidationTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/company/7tt8i7jjv67nm7wy')]
class IndexController extends AbstractDashboardController
{
    use FormValidationTrait;

    private const DASHBOARD_COMPANIES_ROUTE = 'app_dashboard_companies_index';
    private const DASHBOARD_TICKETS_ROUTE = 'app_dashboard_tickets_index';

    public function __construct(
        private readonly UserService    $userService,
        private readonly ProjectService $projectService,
        private readonly CompanyService $companyService,
        private readonly MonologService $monologService,
    ) {
    }

    #[Route('/home', name: 'app_dashboard_companies_index')]
    public function index(): Response
    {
        $this->denyAccessUnlessGrantedRoleCustomer();

        $currentUser = $this->getUser();

        $isAdmin = $this->userService->isAdmin($currentUser);

        if (!$isAdmin && !$currentUser->isTeamLeader()) {
            return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
        }

        $companies = $isAdmin
            ? $this->companyService->getAll()
            : [$currentUser->getCompany()];

        return $this->render('dashboard/companies/index.html.twig', [
            'companies' => $companies,
        ]);
    }

    #[Route('/new', name: 'app_dashboard_company_new', methods: 'POST')]
    public function new(Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $name = $this->validate($request->request->get('name'));

        if (!$name) {
            $this->addFlash('warning', 'Name of the company is required.');
            return $this->redirectToRoute(self::DASHBOARD_COMPANIES_ROUTE);
        }

        if ($this->companyService->getByName($name)) {
            $this->addFlash('warning', 'Company already exists.');
            return $this->redirectToRoute(self::DASHBOARD_COMPANIES_ROUTE);
        }

        $email = $this->validateEmail($request->request->get('email'));
        $phone = $this->validate($request->request->get('phone'));
        $street = $this->validate($request->request->get('street'));
        $zip = $this->validate($request->request->get('zip'));
        $city = $this->validate($request->request->get('city'));

        $company = new Company();

        $this->companyService->save(
            $company
                ->setName($name)
                ->setEmail($email)
                ->setPhone($phone)
                ->setStreet($street)
                ->setZip($zip)
                ->setCity($city)
        );

        $this->addFlash('notice', 'New company has been added.');

        return $this->redirectToRoute(self::DASHBOARD_COMPANIES_ROUTE);
    }

    #[Route('/edit/{id}', name: 'app_dashboard_company_edit')]
    public function edit(?string $id): Response
    {
        $this->denyAccessUnlessGrantedRoleCustomer();

        $currentUser = $this->getUser();

        $isAdmin = $this->userService->isAdmin($currentUser);

        if (!$isAdmin && !$currentUser->isTeamLeader()) {
            return $this->redirectToRoute(self::DASHBOARD_COMPANIES_ROUTE);
        }

        $company = $this->companyService->getById(
            $this->validateNumber($id)
        );

        if (!$company) {
            $this->addFlash('warning', 'Company could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_COMPANIES_ROUTE);
        }

        return $this->render('dashboard/companies/edit.html.twig', [
            'company' => $isAdmin ? $company : $currentUser->getCompany(),
        ]);
    }

    #[Route('/store/{id}', name: 'app_dashboard_company_store', methods: 'POST')]
    public function store(?string $id, Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGrantedRoleCustomer();

        $currentUser = $this->getUser();

        $isAdmin = $this->userService->isAdmin($currentUser);

        if (!$isAdmin && !$currentUser->isTeamLeader()) {
            return $this->redirectToRoute(self::DASHBOARD_COMPANIES_ROUTE);
        }

        $name = $this->validate($request->request->get('name'));

        if (!$name) {
            $this->addFlash('warning', 'Name of the company is required.');
            return $this->redirectToRoute(self::DASHBOARD_COMPANIES_ROUTE);
        }

        $company = $isAdmin
            ? $this->companyService->getById($this->validateNumber($id))
            : $currentUser->getCompany();

        if (!$company) {
            $this->addFlash('warning', 'Company could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_COMPANIES_ROUTE);
        }

        if ($this->companyService->getByName($name) && $name !== $company->getName()) {
            $this->addFlash('warning', 'Company already exists.');
            return $this->redirectToRoute(self::DASHBOARD_COMPANIES_ROUTE);
        }

        $email = $this->validateEmail($request->request->get('email'));
        $phone = $this->validate($request->request->get('phone'));
        $street = $this->validate($request->request->get('street'));
        $zip = $this->validate($request->request->get('zip'));
        $city = $this->validate($request->request->get('city'));

        if ($currentUser->isTeamLeader()) {
            $this->monologService->logger->info(
                sprintf('Old company details %s', json_encode($company->toArray()))
            );
        }

        $company = $this->companyService->save(
            $company
                ->setName($name)
                ->setEmail($email)
                ->setPhone($phone)
                ->setStreet($street)
                ->setZip($zip)
                ->setCity($city)
        );

        if ($currentUser->isTeamLeader()) {
            $this->monologService->logger->info(
                sprintf('New company details %s', json_encode($company->toArray()))
            );
        }

        $this->addFlash('notice', 'Changes has been saved.');

        return $this->redirectToRoute(self::DASHBOARD_COMPANIES_ROUTE);
    }

    #[Route('/delete/{id}', name: 'app_dashboard_company_delete', methods: 'POST')]
    public function delete(?string $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $company = $this->companyService->getById(
            $this->validateNumber($id)
        );

        if (!$company) {
            $this->addFlash('warning', 'Company could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_COMPANIES_ROUTE);
        }

        $this->companyService->save($company->setDeleted(true));

        $this->addFlash('success', 'Company has been deleted.');

        return $this->redirectToRoute(self::DASHBOARD_COMPANIES_ROUTE);
    }
}
