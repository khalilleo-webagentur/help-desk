<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Project;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Service\Jobs\ProjectDeletionJob;
use App\Service\ProjectService;
use App\Service\UserService;
use App\Traits\FormValidationTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/project/jobs/e3c0s6h4z5k7x5r1')]
class JobsController extends AbstractDashboardController
{
    use FormValidationTrait;

    private const string DASHBOARD_PROJECTS_ROUTE = 'app_dashboard_projects_index';

    public function __construct(
        private readonly UserService            $userService,
        private readonly ProjectService         $projectService,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/delete/project/{id}', name: 'app_dashboard_project_delete_project_and_all_related_data', methods: 'POST')]
    public function delete(?string $id, Request $request, ProjectDeletionJob $projectJob): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $user = $this->getUser();
        $password = $this->validate($request->request->get('pw', ''));

        if (!$this->userService->isPasswordValid($user, $password)) {
            $this->addFlash('danger', 'Password is not correct. Please type your password to continue.');
            return $this->redirectToRoute(self::DASHBOARD_PROJECTS_ROUTE);
        }

        $project = $this->projectService->getById(
            $this->validateNumber($request->request->get('pId')),
        );

        if (!$project || $this->validateNumber($id) !== $project->getId()) {
            $this->addFlash('warning', 'Project could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_PROJECTS_ROUTE);
        }

        $projectTitle = $this->validate($request->request->get('name'));

        if ($projectTitle !== $project->getTitle()) {
            $this->addFlash('warning', 'Type Project title correctly and try again.');
            return $this->redirectToRoute('app_dashboard_project_edit', ['id' => $project->getId()]);
        }

        $projectJob->deleteEverythingRelatedToProject($project, $this->entityManager)
            ? $this->addFlash('success', 'The project and its related data has been successfully deleted.')
            : $this->addFlash('warning', 'The project and its related data could not be deleted.');

        return $this->redirectToRoute(self::DASHBOARD_PROJECTS_ROUTE);
    }
}
