<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\SystemLog;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Helper\AppHelper;
use App\Service\SystemLogsService;
use App\Traits\FormValidationTrait;
use DateTime;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/system-logs/sdv1l4u9a0k2x8u7')]
class IndexController extends AbstractDashboardController
{
    use FormValidationTrait;

    private const string DASHBOARD_SYSTEM_LOGS_ROUTE = 'app_dashboard_system_logs_index';

    public function __construct(
        private readonly SystemLogsService $systemLogsService,
    ) {
    }

    #[Route('/i5h8u6v0/{limit?}', name: 'app_dashboard_system_logs_index')]
    public function index(?string $limit): Response
    {
        $this->denyAccessUnlessGrantedRoleSuperAdmin();
        $limit = $limit ? 1000 : AppHelper::DEFAULT_LIMIT_SYSTEM_LOGS_ENTRIES;
        $systemLogs = $this->systemLogsService->getAllWithLimit($limit);

        return $this->render('dashboard/system-logs/index.html.twig', [
            'systemLogs' => $systemLogs,
            'limit' => AppHelper::DEFAULT_LIMIT_SYSTEM_LOGS_ENTRIES,
            'maxLimit' => AppHelper::DEFAULT_MAX_LIMIT_SYSTEM_LOGS_ENTRIES,
            'dateTimeFrom' => (new DateTime())->modify('-1 month')->format('Y-m-d'),
            'dateTimeTo' => (new DateTime())->format('Y-m-d'),
            'logEvents' => AppHelper::SYSTEM_LOG_EVENTS,
        ]);
    }

    #[Route('/v5h8uw70/{id}', name: 'app_dashboard_system_log_view')]
    public function view(?string $id): Response
    {
        $this->denyAccessUnlessGrantedRoleSuperAdmin();

        $systemLog = $this->systemLogsService->getById(
            $this->validateNumber($id)
        );

        if (!$systemLog) {
            $this->addFlash('warning', 'System log not found.');
            return $this->redirectToRoute(self::DASHBOARD_SYSTEM_LOGS_ROUTE);
        }

        return $this->render('dashboard/system-logs/view.html.twig', [
            'systemLog' => $systemLog,
        ]);
    }
}
