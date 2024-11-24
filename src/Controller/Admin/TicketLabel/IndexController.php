<?php

declare(strict_types=1);

namespace App\Controller\Admin\TicketLabel;

use App\Entity\TicketLabel;
use App\Service\TicketLabelsService;
use App\Traits\FormValidationTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/ticket-labels/62o8i0p1v6sgp2wk')]
class IndexController extends AbstractController
{
    use FormValidationTrait;

    private const ADMIN_TICKET_LABELS_ROUTE = 'app_admin_ticket_labels_index';

    public function __construct(
        private readonly TicketLabelsService $ticketLabelsService
    ){
    }

    #[Route('/home', name: 'app_admin_ticket_labels_index')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $ticketLabels = $this->ticketLabelsService->getAll();

        return $this->render('admin/ticket-labels/index.html.twig', [
            'ticketLabels' => $ticketLabels,
        ]);
    }

    #[Route('/new', name: 'app_admin_ticket_label_new', methods: 'POST')]
    public function new(Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $name = $this->validate($request->request->get('name'));
        $color = $this->validate($request->request->get('color'));

        if (!$name || !$color) {
            $this->addFlash('warning', 'Name and color are required.');
            return $this->redirectToRoute(self::ADMIN_TICKET_LABELS_ROUTE);
        }

        if ($this->ticketLabelsService->getOneByName($name)) {
            $this->addFlash('warning', 'Name already exists.');
            return $this->redirectToRoute(self::ADMIN_TICKET_LABELS_ROUTE);
        }

        $description = $this->validateTextarea($request->request->get('description'));

        $newLabel = new TicketLabel();

        $this->ticketLabelsService->save(
            $newLabel
                ->setName($name)
                ->setColor($color)
                ->setDescription($description)
        );

        $this->addFlash('notice', 'New Label has been added.');

        return $this->redirectToRoute(self::ADMIN_TICKET_LABELS_ROUTE);
    }

    #[Route('/edit/{id}', name: 'app_admin_ticket_label_edit')]
    public function edit(?string $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $ticketLabel = $this->ticketLabelsService->getById(
            $this->validateNumber($id)
        );

        if (!$ticketLabel) {
            $this->addFlash('warning', 'User could not be found.');
            return $this->redirectToRoute(self::ADMIN_TICKET_LABELS_ROUTE);
        }

        return $this->render('admin/ticket-labels/edit.html.twig', [
            'ticketLabel' => $ticketLabel,
        ]);
    }

    #[Route('/store/{id}', name: 'app_admin_ticket_label_store', methods: 'POST')]
    public function store(?string $id, Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $name = $this->validate($request->request->get('name'));

        $color = $this->validate($request->request->get('color'));

        if (!$name || !$color) {
            $this->addFlash('warning', 'Name and Email are required.');
            return $this->redirectToRoute(self::ADMIN_TICKET_LABELS_ROUTE);
        }

        $ticketLabel = $this->ticketLabelsService->getById(
            $this->validateNumber($id)
        );

        if (!$ticketLabel) {
            $this->addFlash('warning', 'User could not be found.');
            return $this->redirectToRoute(self::ADMIN_TICKET_LABELS_ROUTE);
        }

        if ($this->ticketLabelsService->getOneByName($name) && $name !== $ticketLabel->getName()) {
            $this->addFlash('warning', 'Name already exists.');
            return $this->redirectToRoute(self::ADMIN_TICKET_LABELS_ROUTE);
        }

        $description = $this->validateTextarea($request->request->get('description'));

        $this->ticketLabelsService->save(
            $ticketLabel
                ->setName($name)
                ->setColor($color)
                ->setDescription($description)
        );

        $this->addFlash('notice', 'Changes has been saved.');

        return $this->redirectToRoute(self::ADMIN_TICKET_LABELS_ROUTE);
    }

    #[Route('/delete/{id}', name: 'app_admin_ticket_label_delete', methods: 'POST')]
    public function delete(?string $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $ticketLabel = $this->ticketLabelsService->getById(
            $this->validateNumber($id)
        );

        if (!$ticketLabel) {
            $this->addFlash('warning', 'User could not be found.');
            return $this->redirectToRoute(self::ADMIN_TICKET_LABELS_ROUTE);
        }

        $this->ticketLabelsService->delete($ticketLabel);

        $this->addFlash('success', 'Ticket-Label has been deleted.');

        return $this->redirectToRoute(self::ADMIN_TICKET_LABELS_ROUTE);
    }
}
