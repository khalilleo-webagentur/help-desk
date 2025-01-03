<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Setting;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Service\UserService;
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
        private readonly UserService $userService,
        private readonly UserSettingService $userSettingService,
    ) {
    }

    #[Route('/home', name: 'app_dashboard_settings_index')]
    public function index(): Response
    {
        $this->denyAccessUnlessGrantedRoleCustomer();

        $settings = $this->userSettingService->getAll();

        $notifyCustomerOnTicketStatusClosed = $this->userSettingService->notifyCustomerOnTicketStatusClosed($this->getUser());

        return $this->render('dashboard/settings/index.html.twig', [
            'settings' => $settings,
            'notifyCustomerOnTicketStatusClosed' => $notifyCustomerOnTicketStatusClosed,
        ]);
    }

    #[Route('/store', name: 'app_dashboard_settings_store', methods: 'POST')]
    public function store(Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGrantedRoleCustomer();

        $user = $this->userService->getById(
            $this->validateNumber($request->request->get('uId'))
        );

        $setting = $this->userSettingService->getOneByUser($user);

        if ($setting) {
            $this->userSettingService->save($setting->setNotifyCloseTicket(
                !$this->validateCheckbox($request->request->get('config'))
            ));
            $this>$this->addFlash('success', 'Setting has been updated');
        }

        return $this->redirectToRoute($request->request->get('path'));
    }
}
