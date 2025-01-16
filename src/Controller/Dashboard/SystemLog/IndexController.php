<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\SystemLog;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Service\SystemLogsService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/system-logs/sdv1l4u9a0k2x8u7')]
class IndexController extends AbstractDashboardController
{
    private const DASHBOARD_SYSTEM_LOGS_ROUTE = 'app_dashboard_system_logs_index';

    public function __construct(
        private readonly SystemLogsService $systemLogsService,
    ) {
    }

    #[Route('/i5h8u6v0/{limit?}', name: 'app_dashboard_system_logs_index')]
    public function index(?string $limit): Response
    {
        $this->denyAccessUnlessGrantedRoleCustomer();
        $limit = $limit ? 1000 : 50;
        $systemLogs = $this->systemLogsService->getAllWithLimit($limit);

        return $this->render('dashboard/system-logs/index.html.twig', [
            'systemLogs' => $systemLogs,
        ]);
    }
}
