<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Project;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Entity\Project;
use App\Service\CompanyService;
use App\Service\ProjectService;
use App\Service\UserService;
use App\Traits\FormValidationTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/project/e3c0s6h4z5k7x5r1')]
class IndexController extends AbstractDashboardController
{
    use FormValidationTrait;

    private const string DASHBOARD_PROJECTS_ROUTE = 'app_dashboard_projects_index';
    private const string DASHBOARD_TICKETS_ROUTE = 'app_dashboard_tickets_index';

    public function __construct(
        private readonly UserService    $userService,
        private readonly ProjectService $projectService,
        private readonly CompanyService $companyService,
    ) {
    }

    #[Route('/home', name: 'app_dashboard_projects_index')]
    public function index(): Response
    {
        $this->denyAccessUnlessGrantedRoleCustomer();

        $user = $this->getUser();

        $isSuperAdmin = $this->isSuperAdmin();

        if (!$isSuperAdmin && !$user->isTeamLeader()) {
            return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
        }

        $projects = $isSuperAdmin
            ? $this->projectService->getAll()
            : $this->projectService->getAllByCompany($user->getCompany());

        $companies = $isSuperAdmin ? $this->companyService->getAll() : [];

        return $this->render('dashboard/projects/index.html.twig', [
            'projects' => $projects,
            'companies' => $companies,
        ]);
    }

    #[Route('/new', name: 'app_dashboard_project_new', methods: 'POST')]
    public function new(Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGrantedRoleCustomer();

        $title = $this->validate($request->request->get('title'));

        if (!$title) {
            $this->addFlash('warning', 'Title of thr project is required.');
            return $this->redirectToRoute(self::DASHBOARD_PROJECTS_ROUTE);
        }

        if ($this->projectService->getOneByTitle($title)) {
            $this->addFlash('warning', 'Project already exists.');
            return $this->redirectToRoute(self::DASHBOARD_PROJECTS_ROUTE);
        }

        $user = $this->getUser();
        $isSuperAdmin = $this->isSuperAdmin();

        $company = $this->companyService->getById(
            $isSuperAdmin
                ? $this->validateNumber($request->request->get('company'))
                : $user->getCompany()->getId()
        );

        if (!$company) {
            $this->addFlash('warning', 'Company not found.');
            return $this->redirectToRoute(self::DASHBOARD_PROJECTS_ROUTE);
        }

        $description = $this->validateTextarea($request->request->get('description'));

        $project = new Project();

        $this->projectService->save(
            $project
                ->setTitle($this->validateNameAndReplaceSpace($title))
                ->setCompany($company)
                ->setDescription($description)
        );

        $this->addFlash(
            'success',
            'New project has been added. Please add a short description and a URL if possible.'
        );

        return $this->redirectToRoute(self::DASHBOARD_PROJECTS_ROUTE);
    }

    #[Route('/edit/{id}', name: 'app_dashboard_project_edit')]
    public function edit(?string $id): Response
    {
        $this->denyAccessUnlessGrantedRoleCustomer();

        $user = $this->getUser();
        $project = null;

        if ($this->userService->isTeamLeader($user)) {
            $project = $this->projectService->getByCompanyAndId(
                $user->getCompany(),
                $this->validateNumber($id)
            );
        }

        $isSuperAdminOrNinja = $this->isSuperAdmin() || $user->isNinja();

        if ($isSuperAdminOrNinja) {
            $project = $this->projectService->getById($this->validateNumber($id));
        }

        if (!$project) {
            $this->addFlash('warning', 'Project could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_PROJECTS_ROUTE);
        }

        return $this->render('dashboard/projects/edit.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/store/{id}', name: 'app_dashboard_project_store', methods: 'POST')]
    public function store(?string $id, Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGrantedRoleCustomer();
        $user = $this->getUser();

        $title = $this->validate($request->request->get('title'));

        if (!$title) {
            $this->addFlash('warning', 'Title of the project is required.');
            return $this->redirectToRoute(self::DASHBOARD_PROJECTS_ROUTE);
        }

        $project = null;

        if ($this->userService->isTeamLeader($user)) {
            $project = $this->projectService->getByCompanyAndId(
                $user->getCompany(),
                $this->validateNumber($id)
            );
        }

        $isSuperAdminOrNinja = $this->isSuperAdmin() || $user->isNinja();

        if ($isSuperAdminOrNinja) {
            $project = $this->projectService->getById($this->validateNumber($id));
        }

        if (!$project) {
            $this->addFlash('warning', 'Project could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_PROJECTS_ROUTE);
        }

        if ($this->projectService->getOneByTitle($title) && $title !== $project->getTitle()) {
            $this->addFlash('warning', 'Project already exists.');
            return $this->redirectToRoute(self::DASHBOARD_PROJECTS_ROUTE);
        }

        $description = $this->validateTextarea($request->request->get('description'));
        $url = $this->validateURL($request->request->get('url'));

        $this->projectService->save(
            $project
                ->setTitle($this->validateNameAndReplaceSpace($title))
                ->setUrl($url)
                ->setDescription($description)
        );

        $this->addFlash('notice', 'Changes has been saved.');

        return $this->redirectToRoute(self::DASHBOARD_PROJECTS_ROUTE);
    }

    #[Route('/delete/{id}', name: 'app_dashboard_project_delete', methods: 'POST')]
    public function delete(?string $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $project = $this->projectService->getById(
            $this->validateNumber($id)
        );

        if (!$project) {
            $this->addFlash('warning', 'Project could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_PROJECTS_ROUTE);
        }

        $this->projectService->delete($project, true);

        $this->addFlash('success', 'Project has been deleted.');

        return $this->redirectToRoute(self::DASHBOARD_PROJECTS_ROUTE);
    }
}
