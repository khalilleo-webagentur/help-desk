<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\TicketType;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Entity\TicketType;
use App\Service\TicketTypesService;
use App\Traits\FormValidationTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/ticket-types/l6a4g6f7n8a5h9k2')]
class IndexController extends AbstractDashboardController
{
    use FormValidationTrait;

    private const DASHBOARD_TICKET_TYPES_ROUTE = 'app_dashboard_ticket_types_index';

    public function __construct(
        private readonly TicketTypesService $ticketTypesService
    ) {
    }

    #[Route('/home', name: 'app_dashboard_ticket_types_index')]
    public function index(): Response
    {
        $this->denyAccessUnlessGrantedRoleCustomer();

        $ticketTypes = $this->ticketTypesService->getAll();

        return $this->render('dashboard/ticket-types/index.html.twig', [
            'ticketTypes' => $ticketTypes,
        ]);
    }

    #[Route('/new', name: 'app_dashboard_ticket_type_new', methods: 'POST')]
    public function new(Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $name = $this->validate($request->request->get('name'));

        if (!$name) {
            $this->addFlash('warning', 'Type of ticket is required.');
            return $this->redirectToRoute(self::DASHBOARD_TICKET_TYPES_ROUTE);
        }

        if ($this->ticketTypesService->getOneByName($name)) {
            $this->addFlash('warning', 'Type already exists.');
            return $this->redirectToRoute(self::DASHBOARD_TICKET_TYPES_ROUTE);
        }

        $description = $this->validateTextarea($request->request->get('description'));

        $newType = new TicketType();

        $this->ticketTypesService->save(
            $newType
                ->setName($name)
                ->setDescription($description)
        );

        $this->addFlash('notice', 'New Type has been added.');

        return $this->redirectToRoute(self::DASHBOARD_TICKET_TYPES_ROUTE);
    }

    #[Route('/edit/{id}', name: 'app_dashboard_ticket_type_edit')]
    public function edit(?string $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $ticketType = $this->ticketTypesService->getById(
            $this->validateNumber($id)
        );

        if (!$ticketType) {
            $this->addFlash('warning', 'Ticket-Type could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_TICKET_TYPES_ROUTE);
        }

        return $this->render('dashboard/ticket-types/edit.html.twig', [
            'ticketType' => $ticketType,
        ]);
    }

    #[Route('/store/{id}', name: 'app_dashboard_ticket_type_store', methods: 'POST')]
    public function store(?string $id, Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $name = $this->validate($request->request->get('name'));

        if (!$name) {
            $this->addFlash('warning', 'Ticket-Type is required.');
            return $this->redirectToRoute(self::DASHBOARD_TICKET_TYPES_ROUTE);
        }

        $ticketType = $this->ticketTypesService->getById(
            $this->validateNumber($id)
        );

        if (!$ticketType) {
            $this->addFlash('warning', 'Ticket-Type could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_TICKET_TYPES_ROUTE);
        }

        if ($this->ticketTypesService->getOneByName($name) && $name !== $ticketType->getName()) {
            $this->addFlash('warning', 'Ticket-Type already exists.');
            return $this->redirectToRoute(self::DASHBOARD_TICKET_TYPES_ROUTE);
        }

        $description = $this->validateTextarea($request->request->get('description'));

        $this->ticketTypesService->save(
            $ticketType
                ->setName($name)
                ->setDescription($description)
        );

        $this->addFlash('notice', 'Changes has been saved.');

        return $this->redirectToRoute(self::DASHBOARD_TICKET_TYPES_ROUTE);
    }

    #[Route('/delete/{id}', name: 'app_dashboard_ticket_type_delete', methods: 'POST')]
    public function delete(?string $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $ticketType = $this->ticketTypesService->getById(
            $this->validateNumber($id)
        );

        if (!$ticketType) {
            $this->addFlash('warning', 'Ticket-Type could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_TICKET_TYPES_ROUTE);
        }

        $this->ticketTypesService->delete($ticketType);

        $this->addFlash('success', 'Ticket-Type has been deleted.');

        return $this->redirectToRoute(self::DASHBOARD_TICKET_TYPES_ROUTE);
    }
}
