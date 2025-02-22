<?php

declare(strict_types=1);

namespace App\Controller\Dashboard\Message;

use App\Controller\Dashboard\AbstractDashboardController;
use App\Helper\AppHelper;
use App\Mails\Admin\ContactFormNewMessageMail;
use App\Service\MessagesService;
use App\Service\UserService;
use App\Traits\FormValidationTrait;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/b3z3d3k9/{limit?}', name: 'app_dashboard_message_index')]
    public function index(?string $limit): Response
    {
        $this->denyAccessUnlessGrantedRoleCustomer();
        $limit = $limit ? 1000 : AppHelper::DEFAULT_LIMIT_MESSAGES_ENTRIES;
        $user = $this->getUser();
        $isAdmin = $this->userService->isAdmin($user);

        $messages = $isAdmin
            ? $this->messagesService->getAllWithLimit($limit)
            : $this->messagesService->getAllByEmailWithLimit($user->getUserIdentifier(), $limit);

        $users = $isAdmin
            ? $this->userService->getAllCustomers()
            : [];

        return $this->render('dashboard/messages/index.html.twig', [
            'messages' => $messages,
            'limit' => AppHelper::DEFAULT_LIMIT_MESSAGES_ENTRIES,
            'maxLimit' => AppHelper::DEFAULT_MAX_LIMIT_MESSAGES_ENTRIES,
            'users' => $users,
        ]);
    }

    #[Route('/e3t4mti7', name: 'app_dashboard_message_new', methods: ['POST'])]
    public function new(Request $request, ContactFormNewMessageMail $contactFormNewMessageMail): Response
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
        $username = $user->getName();
        $email = $user->getUserIdentifier();

        if ($this->userService->isAdmin($user)) {
            $targetUser = $this->userService->getById($this->validateNumber(
                $request->request->get('uId')
            ));
            $username = $targetUser->getName();
            $email = $targetUser->getEmail();
        }

        $this->messagesService->create($username, $email, $subject, $message);

        // @TODO add configuration notifySendMessage ...

        $contactFormNewMessageMail->send([]);

        $this->addFlash('success', 'Your message has been sent successfully.');

        return $redirectTo;
    }

    #[Route('/l8t4c3i7/{id}', name: 'app_dashboard_message_view')]
    public function view(?string $id): Response
    {
        $this->denyAccessUnlessGrantedRoleCustomer();
        $user = $this->getUser();
        $messageId = $this->validateNumber($id);

        $message = $this->userService->isAdmin($user)
            ? $this->messagesService->getById($messageId)
            : $this->messagesService->getByEmailAndId($user->getUserIdentifier(), $messageId);

        if (!$message) {
            $this->addFlash('warning', 'Message could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_MESSAGES_ROUTE);
        }

        if ($message->getEmail() !== $user->getUserIdentifier()) {
            $this->messagesService->updateIsSeen($message);
        }

        return $this->render('dashboard/messages/view.html.twig', [
            'message' => $message,
        ]);
    }

    #[Route('/d3t4lti7/{id}', name: 'app_dashboard_message_delete', methods: ['POST'])]
    public function delete(?string $id): Response
    {
        $this->denyAccessUnlessGrantedRoleCustomer();
        $user = $this->getUser();
        $messageId = $this->validateNumber($id);

        $message = $this->userService->isAdmin($user)
            ? $this->messagesService->getById($messageId)
            : $this->messagesService->getByEmailAndId($user->getUserIdentifier(), $messageId);

        if (!$message) {
            $this->addFlash('warning', 'Message could not be found.');
            return $this->redirectToRoute(self::DASHBOARD_MESSAGES_ROUTE);
        }

        $this->messagesService->delete($message);

        return $this->redirectToRoute(self::DASHBOARD_MESSAGES_ROUTE);
    }
}
