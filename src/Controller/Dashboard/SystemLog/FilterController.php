<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\SystemLog;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Helper\AppHelper;
use App\Service\SystemLogsService;
use App\Traits\FormValidationTrait;
use DateTime;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/system-logs/filter/fm6e5m4y8n4del')]
class FilterController extends AbstractDashboardController
{
    use FormValidationTrait;

    private const string DASHBOARD_SYSTEM_LOGS_ROUTE = 'app_dashboard_system_logs_index';

    public function __construct(
        private readonly SystemLogsService $systemLogsService,
    ) {
    }

    #[Route('/q-logs', name: 'app_dashboard_system_logs_filter_delete', methods: ['POST'])]
    public function delete(Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGrantedRoleSuperAdmin();

        $dateFrom = $this->validate($request->request->get('dateFrom'));
        $dateTo = $this->validate($request->request->get('dateTo'));

        $logDateFrom = DateTime::createFromFormat('Y-m-d', $dateFrom);
        $logDateTo = DateTime::createFromFormat('Y-m-d', $dateTo);

        if (false === $logDateFrom || false === $logDateTo) {
            $this->addFlash('warning', 'Invalid date format');
            return $this->redirectToRoute(self::DASHBOARD_SYSTEM_LOGS_ROUTE);
        }

        $logDateFrom->modify('00:00:00');
        $logDateTo->modify('23:59:59');
        $event = $this->validate($request->request->get('event'));

        if ($event === 'all' || !in_array($event, AppHelper::SYSTEM_LOG_EVENTS, true)) {
            $event = '';
        }

        $countDeletedLogs = $this->systemLogsService->deleteAllByCriteria($event, $logDateFrom, $logDateTo);

        if ($countDeletedLogs > 0) {
            $message = sprintf('System-Logs [%s] have been deleted.', $countDeletedLogs);
            $this->systemLogsService->create($event, $event);
            $this->addFlash('success', $message);
            return $this->redirectToRoute(self::DASHBOARD_SYSTEM_LOGS_ROUTE);
        }

        $this->addFlash(
            'notice',
            sprintf(
                'No Logs between %s and %s have been founded.',
                $logDateFrom->format('Y-m-d'),
                $logDateTo->format('Y-m-d')
            )
        );

        return $this->redirectToRoute(self::DASHBOARD_SYSTEM_LOGS_ROUTE);
    }
}
