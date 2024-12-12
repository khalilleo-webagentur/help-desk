<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Ticket;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Service\TicketService;
use App\Service\UserService;
use App\Traits\FormValidationTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/ticket/filter/xfo4dx3g1d6sx47vk')]
class FilterController extends AbstractDashboardController
{
    use FormValidationTrait;

    public function __construct(
        private readonly UserService   $userService,
        private readonly TicketService $ticketService,
    ) {
    }

    #[Route('/filter', name: 'app_dashboard_ticket_filter', methods: ['GET', 'POST'])]
    public function index(Request $request): RedirectResponse|Response
    {
        $this->denyAccessUnlessGrantedRoleSuperAdmin();
        $user = $this->getUser();

        $companyId = $this->validateNumber($request->request->get('iO7n2sO22'));
        $projectId = $this->validateNumber($request->request->get('4yT4bG2sO'));
        $labelId = $this->validateNumber($request->request->get('nM4yT4'));
        $statusId = $this->validateNumber($request->request->get('iO7nM4yT4'));
        $issueByName = $this->validate($request->request->get('V9xO'));
        $assigneeName = $this->validate($request->request->get('m79xzO'));
        $ticketNo = $this->validate($request->request->get('3aD7v78Tic'));
        $ticketTitle = $this->validate($request->request->get('aD7v78Ti'));
        $dateFrom = $this->validate($request->request->get('D7v78dateT'));
        $dateTo = $this->validate($request->request->get('vv78dateT'));

        // @TODO ...

        return $this->render('dashboard/tickets/search.html.twig');
    }
}
