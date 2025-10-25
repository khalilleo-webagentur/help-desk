<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Tools;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Helper\AppHelper;
use App\Service\Core\FileHandlerService;
use App\Service\DeletableFilesService;
use App\Traits\FormValidationTrait;
use DateTime;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/tools/deletable-files/h6a7g6fhn8arh9n')]
class DeletableFilesController extends AbstractDashboardController
{
    use FormValidationTrait;

    private const string DASHBOARD_TICKETS_ROUTE = 'app_dashboard_tickets_index';
    private const string APP_DASHBOARD_TOOLS_DELETABLE_FILES_INDEX = 'app_dashboard_tools_deletable_files_index';

    public function __construct(
        private readonly DeletableFilesService $deletableFilesService
    ) {
    }

    #[Route('/de9l7ols4', name: 'app_dashboard_tools_deletable_files_index')]
    public function index(): Response
    {
        $this->denyAccessUnlessGrantedRoleSuperAdmin();

        $deletableFiles = $this->deletableFilesService->getAll();

        return $this->render('dashboard/tools/deletable-files/index.html.twig', [
            'deletableFiles' => $deletableFiles,
            'directories' => AppHelper::DIRECTORIES,
            'dateTimeFrom' => new DateTime()->modify('-3 month')->format('Y-m-d'),
            'dateTimeTo' => new DateTime()->modify('-1 month')->format('Y-m-d'),
        ]);
    }

    #[Route('/file/{hash}', name: 'app_dashboard_tools_deletable_file_show', methods: ['GET', 'POST'])]
    public function view(?string $hash, Request $request, FileHandlerService $fileHandlerService): Response
    {
        if ($request->getMethod() === 'GET') {
            $this->addFlash('notice', 'The file that you are looking for could not be found.');
            return $this->redirectToRoute('app_home');
        }

        $this->denyAccessUnlessGrantedRoleSuperAdmin();

        $deletableFile = $this->deletableFilesService->getById(
            $this->validateNumber($request->request->get('id')),
        );

        if (!$deletableFile) {
            $this->addFlash('warning', 'The file that you are looking for could not be found.');
            return $this->redirectToRoute('app_dashboard_tools_deletable_files_index');
        }

        $dir = $this->getParameter($deletableFile->getDirectory());

        if (!$fileHandlerService->isFileExistsInDir($dir, $deletableFile->getFilename())) {
            $this->addFlash('warning', 'File could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_TICKETS_ROUTE);
        }

        return $this->file($dir . '/' . $deletableFile->getFilename(), $hash, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    #[Route('/delete-files', name: 'app_dashboard_tools_deletable_files_delete', methods: ['POST'])]
    public function delete(Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGrantedRoleSuperAdmin();

        $directory = $this->validate($request->request->get('directory'));

        if (!$directory) {
            $this->addFlash('warning', 'Directory field is required.');
            return $this->redirectToRoute(self::APP_DASHBOARD_TOOLS_DELETABLE_FILES_INDEX);
        }

        $iDateFrom = $this->validate($request->request->get('dateFrom'));
        $iDateTo = $this->validate($request->request->get('dateTo'));

        if (!$iDateFrom || !$iDateTo) {
            $this->addFlash('warning', 'Date fields are required.');
            return $this->redirectToRoute(self::APP_DASHBOARD_TOOLS_DELETABLE_FILES_INDEX);
        }

        $dateFrom = DateTime::createFromFormat('Y-m-d', $iDateFrom);
        $dateTo = DateTime::createFromFormat('Y-m-d', $iDateTo);

        if (false === $dateFrom || false === $dateTo) {
            $this->addFlash('warning', 'Invalid date format');
            return $this->redirectToRoute(self::APP_DASHBOARD_TOOLS_DELETABLE_FILES_INDEX);
        }

        $count = $this->deletableFilesService->deleteAllByCriteria($directory, $dateFrom, $dateTo);
        $count > 0
            ? $this->addFlash('success', sprintf('Files (%s) has been successfully Deleted.', $count))
            : $this->addFlash('notice', 'No files has been deleted.');

        return $this->redirectToRoute(self::APP_DASHBOARD_TOOLS_DELETABLE_FILES_INDEX);
    }
}
