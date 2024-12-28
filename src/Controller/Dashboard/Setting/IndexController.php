<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Setting;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Service\UserSettingService;
use App\Traits\FormValidationTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/settings/seb8y5m8x4w4c2ng')]
class IndexController extends AbstractDashboardController
{
    use FormValidationTrait;

    private const DASHBOARD_SETTINGS_ROUTE = 'app_dashboard_settings_index';

    public function __construct(
        private readonly UserSettingService $userSettingService
    ) {
    }

    #[Route('/home', name: 'app_dashboard_settings_index')]
    public function index(): Response
    {
        $this->denyAccessUnlessGrantedRoleCustomer();

        $notifyCustomerOnTicketStatusClosed = $this->userSettingService->notifyCustomerOnTicketStatusClosed($this->getUser());

        return $this->render('dashboard/settings/index.html.twig', [
            'notifyCustomerOnTicketStatusClosed' => $notifyCustomerOnTicketStatusClosed,
        ]);
    }

    #[Route('/store', name: 'app_dashboard_settings_store', methods: ['POST'])]
    public function store(Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGrantedRoleCustomer();

        $config = !$this->validateCheckbox($request->request->get('c1z3n6t4'));

        $user = $this->getUser();

        $setting = $this->userSettingService->getOneByUser($user);

        $this->userSettingService->save($setting->setNotifyCloseTicket($config));

        $this>$this->addFlash('success', 'Setting has been updated');

        return $this->redirectToRoute(self::DASHBOARD_SETTINGS_ROUTE);
    }
}
