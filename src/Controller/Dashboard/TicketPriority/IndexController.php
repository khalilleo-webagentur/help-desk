<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\TicketPriority;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Entity\TicketPriority;
use App\Service\TicketPriorityService;
use App\Traits\FormValidationTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/ticket-priorities/zhb8y5m8x4w4c287')]
class IndexController extends AbstractDashboardController
{
    use FormValidationTrait;

    private const string DASHBOARD_TICKET_PRIORITIES_ROUTE = 'app_dashboard_ticket_priorities_index';

    public function __construct(
        private readonly TicketPriorityService $ticketPriorityService
    ) {
    }

    #[Route('/home', name: 'app_dashboard_ticket_priorities_index')]
    public function index(): Response
    {
        $this->denyAccessUnlessGrantedRoleCustomer();

        $ticketPriorities = $this->ticketPriorityService->getAll();

        return $this->render('dashboard/ticket-priorities/index.html.twig', [
            'ticketPriorities' => $ticketPriorities,
        ]);
    }

    #[Route('/new', name: 'app_dashboard_ticket_priority_new', methods: 'POST')]
    public function new(Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $name = $this->validate($request->request->get('name'));
        $color = $this->validate($request->request->get('color'));

        if (!$name || !$color) {
            $this->addFlash('warning', 'Name and color are required.');
            return $this->redirectToRoute(self::DASHBOARD_TICKET_PRIORITIES_ROUTE);
        }

        if ($this->ticketPriorityService->getOneByName($name)) {
            $this->addFlash('warning', 'Name already exists.');
            return $this->redirectToRoute(self::DASHBOARD_TICKET_PRIORITIES_ROUTE);
        }

        $description = $this->validateTextarea($request->request->get('description'));

        $newPriority = new TicketPriority();

        $this->ticketPriorityService->save(
            $newPriority
                ->setName($name)
                ->setTextColor($color)
                ->setDescription($description)
        );

        $this->addFlash('notice', 'New priority has been added.');

        return $this->redirectToRoute(self::DASHBOARD_TICKET_PRIORITIES_ROUTE);
    }

    #[Route('/edit/{id}', name: 'app_dashboard_ticket_priority_edit')]
    public function edit(?string $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $ticketPriority = $this->ticketPriorityService->getById(
            $this->validateNumber($id)
        );

        if (!$ticketPriority) {
            $this->addFlash('warning', 'Ticket-Priority could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_TICKET_PRIORITIES_ROUTE);
        }

        return $this->render('dashboard/ticket-priorities/edit.html.twig', [
            'ticketPriority' => $ticketPriority,
        ]);
    }

    #[Route('/store/{id}', name: 'app_dashboard_ticket_priority_store', methods: 'POST')]
    public function store(?string $id, Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $name = $this->validate($request->request->get('name'));

        $color = $this->validate($request->request->get('color'));

        if (!$name || !$color) {
            $this->addFlash('warning', 'Name and color are required.');
            return $this->redirectToRoute(self::DASHBOARD_TICKET_PRIORITIES_ROUTE);
        }

        $ticketPriority = $this->ticketPriorityService->getById(
            $this->validateNumber($id)
        );

        if (!$ticketPriority) {
            $this->addFlash('warning', 'Ticket-Priority could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_TICKET_PRIORITIES_ROUTE);
        }

        if ($this->ticketPriorityService->getOneByName($name) && $name !== $ticketPriority->getName()) {
            $this->addFlash('warning', 'Name already exists.');
            return $this->redirectToRoute(self::DASHBOARD_TICKET_PRIORITIES_ROUTE);
        }

        $description = $this->validateTextarea($request->request->get('description'));

        $this->ticketPriorityService->save(
            $ticketPriority
                ->setName($name)
                ->setTextColor($color)
                ->setDescription($description)
        );

        $this->addFlash('notice', 'Changes has been saved.');

        return $this->redirectToRoute(self::DASHBOARD_TICKET_PRIORITIES_ROUTE);
    }

    #[Route('/delete/{id}', name: 'app_dashboard_ticket_priority_delete', methods: 'POST')]
    public function delete(?string $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $ticketPriority = $this->ticketPriorityService->getById(
            $this->validateNumber($id)
        );

        if (!$ticketPriority) {
            $this->addFlash('warning', 'Ticket-Priority could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_TICKET_PRIORITIES_ROUTE);
        }

        $this->ticketPriorityService->delete($ticketPriority);

        $this->addFlash('success', 'Ticket-Priority has been deleted.');

        return $this->redirectToRoute(self::DASHBOARD_TICKET_PRIORITIES_ROUTE);
    }
}
