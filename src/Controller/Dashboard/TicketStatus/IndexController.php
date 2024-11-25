<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\TicketStatus;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Entity\TicketStatus;
use App\Service\TicketStatusService;
use App\Traits\FormValidationTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/ticket-status/xco8i0asv6ser2w7')]
class IndexController extends AbstractDashboardController
{
    use FormValidationTrait;

    private const DASHBOARD_TICKET_STATUS_ROUTE = 'app_dashboard_ticket_status_index';

    public function __construct(
        private readonly TicketStatusService $ticketStatusService
    ){
    }

    #[Route('/home', name: 'app_dashboard_ticket_status_index')]
    public function index(): Response
    {
        $this->denyAccessUnlessGrantedRoleCustomer();

        $ticketStatus = $this->ticketStatusService->getAll();

        return $this->render('dashboard/ticket-status/index.html.twig', [
            'ticketStatus' => $ticketStatus,
        ]);
    }

    #[Route('/new', name: 'app_dashboard_ticket_status_new', methods: 'POST')]
    public function new(Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $name = $this->validate($request->request->get('name'));
        $color = $this->validate($request->request->get('color'));

        if (!$name || !$color) {
            $this->addFlash('warning', 'Name and color are required.');
            return $this->redirectToRoute(self::DASHBOARD_TICKET_STATUS_ROUTE);
        }

        if ($this->ticketStatusService->getOneByName($name)) {
            $this->addFlash('warning', 'Name already exists.');
            return $this->redirectToRoute(self::DASHBOARD_TICKET_STATUS_ROUTE);
        }

        $newStatus = new TicketStatus();

        $this->ticketStatusService->save(
            $newStatus
                ->setName($name)
                ->setColor($color)
        );

        $this->addFlash('notice', 'New Ticket-Status has been added.');

        return $this->redirectToRoute(self::DASHBOARD_TICKET_STATUS_ROUTE);
    }

    #[Route('/edit/{id}', name: 'app_dashboard_ticket_status_edit')]
    public function edit(?string $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $ticketStatus = $this->ticketStatusService->getById(
            $this->validateNumber($id)
        );

        if (!$ticketStatus) {
            $this->addFlash('warning', 'Ticket-Status could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_TICKET_STATUS_ROUTE);
        }

        return $this->render('dashboard/ticket-status/edit.html.twig', [
            'ticketStatus' => $ticketStatus,
        ]);
    }

    #[Route('/store/{id}', name: 'app_dashboard_ticket_status_store', methods: 'POST')]
    public function store(?string $id, Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $name = $this->validate($request->request->get('name'));
        $color = $this->validate($request->request->get('color'));

        if (!$name || !$color) {
            $this->addFlash('warning', 'Name and color are required.');
            return $this->redirectToRoute(self::DASHBOARD_TICKET_STATUS_ROUTE);
        }

        $ticketStatus = $this->ticketStatusService->getById(
            $this->validateNumber($id)
        );

        if (!$ticketStatus) {
            $this->addFlash('warning', 'Ticket-Status could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_TICKET_STATUS_ROUTE);
        }

        if ($this->ticketStatusService->getOneByName($name) && $name !== $ticketStatus->getName()) {
            $this->addFlash('warning', 'Name already exists.');
            return $this->redirectToRoute(self::DASHBOARD_TICKET_STATUS_ROUTE);
        }

        $this->ticketStatusService->save(
            $ticketStatus
                ->setName($name)
                ->setColor($color)
        );

        $this->addFlash('notice', 'Changes has been saved.');

        return $this->redirectToRoute(self::DASHBOARD_TICKET_STATUS_ROUTE);
    }

    #[Route('/delete/{id}', name: 'app_dashboard_ticket_status_delete', methods: 'POST')]
    public function delete(?string $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $ticketStatus = $this->ticketStatusService->getById(
            $this->validateNumber($id)
        );

        if (!$ticketStatus) {
            $this->addFlash('warning', 'Ticket-Status could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_TICKET_STATUS_ROUTE);
        }

        $this->ticketStatusService->delete($ticketStatus);

        $this->addFlash('success', 'Ticket-Status has been deleted.');

        return $this->redirectToRoute(self::DASHBOARD_TICKET_STATUS_ROUTE);
    }
}
