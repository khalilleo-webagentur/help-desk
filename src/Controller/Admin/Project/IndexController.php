<?php

declare(strict_types=1);

namespace App\Controller\Admin\Project;

use App\Entity\Project;
use App\Service\ProjectService;
use App\Traits\FormValidationTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/project/91o8i0h1v6lgp7wm')]
class IndexController extends AbstractController
{
    use FormValidationTrait;

    private const DASHBOARD_PROJECTS_ROUTE = 'app_admin_projects_index';

    public function __construct(
        private readonly ProjectService $projectService
    ){
    }

    #[Route('/home', name: 'app_admin_projects_index')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $projects = $this->projectService->getAll();

        return $this->render('admin/projects/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    #[Route('/new', name: 'app_admin_project_new', methods: 'POST')]
    public function new(Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $user = $this->getUser();

        $title = $this->validate($request->request->get('title'));

        if (!$title) {
            $this->addFlash('warning', 'Title of thr project is required.');
            return $this->redirectToRoute(self::DASHBOARD_PROJECTS_ROUTE);
        }

        if ($this->projectService->getOneByTitle($title)) {
            $this->addFlash('warning', 'Project is already exists.');
            return $this->redirectToRoute(self::DASHBOARD_PROJECTS_ROUTE);
        }

        $description = $this->validateTextarea($request->request->get('description'));

        $project = new Project();

        $this->projectService->save(
            $project
                ->setTitle($title)
                ->setUser($user)
                ->setDescription($description)
        );

        $this->addFlash('notice', 'New project has been added.');

        return $this->redirectToRoute(self::DASHBOARD_PROJECTS_ROUTE);
    }

    #[Route('/edit/{id}', name: 'app_admin_project_edit')]
    public function edit(?string $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $project = $this->projectService->getById(
            $this->validateNumber($id)
        );

        if (!$project) {
            $this->addFlash('warning', 'Project could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_PROJECTS_ROUTE);
        }

        return $this->render('admin/projects/edit.html.twig', [
            'project' => $project,
        ]);
    }

    #[Route('/store/{id}', name: 'app_admin_project_store', methods: 'POST')]
    public function store(?string $id, Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $title = $this->validate($request->request->get('title'));

        if (!$title) {
            $this->addFlash('warning', 'Title of thr project is required.');
            return $this->redirectToRoute(self::DASHBOARD_PROJECTS_ROUTE);
        }

        $project = $this->projectService->getById(
            $this->validateNumber($id)
        );

        if (!$project) {
            $this->addFlash('warning', 'Project could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_PROJECTS_ROUTE);
        }

        if ($this->projectService->getOneByTitle($title) && $title !== $project->getTitle()) {
            $this->addFlash('warning', 'Project already exists.');
            return $this->redirectToRoute(self::DASHBOARD_PROJECTS_ROUTE);
        }

        $description = $this->validateTextarea($request->request->get('description'));

        $this->projectService->save(
            $project
                ->setTitle($title)
                ->setDescription($description)
        );

        $this->addFlash('notice', 'Changes has been saved.');

        return $this->redirectToRoute(self::DASHBOARD_PROJECTS_ROUTE);
    }

    #[Route('/delete/{id}', name: 'app_admin_project_delete', methods: 'POST')]
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

        $this->projectService->delete($project);

        $this->addFlash('success', 'Project has been deleted.');

        return $this->redirectToRoute(self::DASHBOARD_PROJECTS_ROUTE);
    }
}
