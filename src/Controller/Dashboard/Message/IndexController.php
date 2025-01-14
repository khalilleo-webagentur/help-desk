<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Message;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Service\MessagesService;
use App\Service\UserService;
use App\Traits\FormValidationTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/messages/e5i5y0g9v8v0l4b2')]
class IndexController extends AbstractDashboardController
{
    use FormValidationTrait;

    private const DASHBOARD_MESSAGES_ROUTE = 'app_dashboard_message_index';

    public function __construct(
        private readonly UserService     $userService,
        private readonly MessagesService $messagesService,
    ) {
    }

    #[Route('/b3z3d3k9', name: 'app_dashboard_message_index')]
    public function index(): Response
    {
        $this->denyAccessUnlessGrantedRoleSuperAdmin();

        $messages = $this->messagesService->getAll();

        return $this->render('dashboard/messages/index.html.twig', [
            'messages' => $messages,
        ]);
    }

    #[Route('/l8t4c3i7/{id}', name: 'app_dashboard_message_view')]
    public function view(?string $id): Response
    {
        $this->denyAccessUnlessGrantedRoleSuperAdmin();

        $messageId = $this->validateNumber($id);

        $message = $this->messagesService->getById($messageId);

        if (!$message) {
            $this->addFlash('warning', 'Message could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_MESSAGES_ROUTE);
        }

        return $this->render('dashboard/messages/view.html.twig', [
            'message' => $message,
        ]);
    }
}
