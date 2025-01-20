<?php

declare(strict_types=1);

namespace App\Controller\Dashboard;

use App\Helper\AppHelper;
use App\Mails\Admin\ContactFormNewMessageMail;
use App\Service\MessagesService;
use App\Service\TicketService;
use App\Service\TicketStatusService;
use App\Service\UserService;
use App\Traits\FormValidationTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('dashboard/i4a7y5t7i2q4w5x2')]
class IndexController extends AbstractDashboardController
{
    use FormValidationTrait;

    public function __construct(
        private readonly UserService         $userService,
        private readonly TicketService       $ticketService,
        private readonly TicketStatusService $ticketStatusService,
        private readonly MessagesService     $messagesService,
    ) {
    }

    #[Route('/home', name: 'app_dashboard_index')]
    public function index(): Response
    {
        $this->denyAccessUnlessGrantedRoleCustomer();

        $user = $this->getUser();

        $isAdmin = $this->userService->isAdmin($user) || $user->isNinja();
        $company = $user->getCompany();

        // all of them
        $countIssues = $isAdmin
            ? $this->ticketService->countAll()
            : $this->ticketService->countAllByCompany($company);

        // only status open
        $status = $this->ticketStatusService->getOneByName(AppHelper::STATUS_OPEN);

        $countOpenIssues = $isAdmin
            ? $this->ticketService->countStatus($status)
            : $this->ticketService->countAllByCompanyAndStatus($company, $status);

        // only status closed
        $status = $this->ticketStatusService->getOneByName(AppHelper::STATUS_CLOSED);

        $countClosedIssues = $isAdmin
            ? $this->ticketService->countStatus($status)
            : $this->ticketService->countAllByCompanyAndStatus($company, $status);

        // only status resolved
        $status = $this->ticketStatusService->getOneByName(AppHelper::STATUS_RESOLVED);

        $countResolvedIssues = $isAdmin
            ? $this->ticketService->countStatus($status)
            : $this->ticketService->countAllByCompanyAndStatus($company, $status);

        // only status in progress
        $status = $this->ticketStatusService->getOneByName(AppHelper::STATUS_IN_PROGRESS);

        $countIssuesInProgress = $isAdmin
            ? $this->ticketService->countStatus($status)
            : $this->ticketService->countAllByCompanyAndStatus($company, $status);

        return $this->render('dashboard/index.html.twig', [
            'countIssues' => $countIssues,
            'countOpenIssues' => $countOpenIssues,
            'countClosedIssues' => $countClosedIssues + $countResolvedIssues,
            'countIssuesInProgress' => $countIssuesInProgress,
        ]);
    }

    #[Route('/e3t4mti7', name: 'app_dashboard_email_us', methods: ['POST'])]
    public function emailUs(Request $request, ContactFormNewMessageMail $contactFormNewMessageMail): Response
    {
        $this->denyAccessUnlessGrantedRoleCustomer();

        $redirectTo = $this->redirectToRoute(
            $this->validate($request->request->get('_backTo'))
        );

        $subject = $this->validate($request->request->get('subject'));

        if (!$subject) {
            $this->addFlash('warning', 'Subject field is required.');
            return $redirectTo;
        }

        $message = $this->validateTextarea($request->request->get('msg'));

        if (!$message) {
            $this->addFlash('warning', 'Message field is required.');
            return $redirectTo;
        }

        $user = $this->getUser();

        $this->messagesService->create(
            $user->getName(),
            $user->getUserIdentifier(),
            $subject,
            $message,
        );

        $contactFormNewMessageMail->send([]);

        $this->addFlash('success', 'Your message has been sent successfully.');

        return $redirectTo;
    }
}
