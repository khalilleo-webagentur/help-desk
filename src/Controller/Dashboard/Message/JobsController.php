<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Message;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Service\MessagesService;
use App\Service\UserService;
use App\Traits\FormValidationTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dashboard/messages/jobs/r5i7y0gmv8v3l4bb')]
class JobsController extends AbstractDashboardController
{
    use FormValidationTrait;

    private const string DASHBOARD_MESSAGES_ROUTE = 'app_dashboard_message_index';

    public function __construct(
        private readonly UserService     $userService,
        private readonly MessagesService $messagesService
    ) {
    }

    #[Route('/empty-bin', name: 'app_dashboard_message_jobs_empty_bin', methods: ['POST'])]
    public function emptyPin(): Response
    {
        $this->denyAccessUnlessGrantedRoleCustomer();

        $countDeletedMessages = $this->messagesService->emptyPin();

        $countDeletedMessages > 0
            ? $this->addFlash('success', sprintf('Messages [%s] are deleted permanently.', $countDeletedMessages))
            : $this->addFlash('notice', 'No messages found.');

        return $this->redirectToRoute(self::DASHBOARD_MESSAGES_ROUTE);
    }
}
